<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\ContentBlocker\ContentBlockerModel;
use Borlabs\Cookie\Repository\ContentBlocker\ContentBlockerRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\ContentBlocker\ContentBlockerService;
use Exception;
use WP_CLI;

/**
 * Lists, creates, updates and deletes the content blocker of the Borlabs Cookie plugin.
 */
class ContentBlockerCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display for each object.
     */
    public const DEFAULT_FIELDS = [
        'id',
        'content-blocker-id',
        'language',
        'name',
        'privacy-policy-url',
        'hosts',
        'status',
        'undeletable',
    ];

    /**
     * @const OPTIONAL_FIELDS Optional field to display for each object.
     */
    public const OPTIONAL_FIELDS = [
        'description',
        'preview-html',
        'preview-css',
        'global-js',
        'init-js',
        // settings
        'unblock-all',
        'execute-global-code-before-unblocking',
    ];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected array $fieldMap = [
        'privacy-policy-url' => 'privacyPolicyUrl',
        'content-blocker-id' => 'contentBlockerId',
        'preview-html' => 'previewHtml',
        'preview-css' => 'previewCSc',
        'global-js' => 'globalJavaScript',
        'init-js' => 'initialJavaScript',
    ];

    /**
     * @var string[] list of attributes that can be filtered / ordered with wp cli
     */
    protected array $orderAndFilterableFields = [
        'id',
        'content-blocker-id',
        'language',
        'name',
        'privacy-policy-url',
        'status',
        'undeletable',
    ];

    private Container $container;

    private ContentBlockerRepository $contentBlockerRepository;

    private ContentBlockerService $contentBlockerService;

    /**
     * ContentBlockerCommand constructor.
     *
     * @throws Exception
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->contentBlockerRepository = $this->container->get(ContentBlockerRepository::class);
        $this->contentBlockerService = $this->container->get(ContentBlockerService::class);
    }

    /**
     * Creates a new content blocker.
     *
     * ## OPTIONS
     *
     * <key>
     * : The key of the content blocker.
     *
     * <language>
     * : The language code (f.e. en, de, ...) of the content blocker.
     *
     * <name>
     * : The name of the content blocker.
     *
     * [--status=<status>]
     * : Whether or not the content blocker will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether or not the content blocker will be undeletable.
     *
     * [--preview-html=<preview-html>]
     * : The preview HTML of the content blocker.
     *
     * [--preview-css=<preview-css>]
     * : The preview CSS of the content blocker.
     *
     * [--global-js=<global-js>]
     * : The global JS of the content blocker.
     *
     * [--init-js=<init-js>]
     * : The init JS of the content blocker.
     *
     * [--description=<description>]
     * : The description of the content blocker.
     *
     * [--privacy-policy-url=<privacy-policy-url>]
     * : The privacy policy URL of the content blocker.
     *
     * [--hosts=<hosts>]
     * : Array of hosts of the content blocker. (f.e. `["somehost.com", "somehost2.com"]`)
     *
     * [--execute-global-code-before-unblocking=<execute-global-code-before-unblocking>]
     * : Whether or not the global code will be executed before unblocking.
     *
     * [--unblock-all=<unblock-all>]
     * : Whether or not unblocking one blocked content unblocks all other on the same page.
     *
     * [--porcelain]
     * : Output just the new content blocker id.
     *
     * ## EXAMPLES
     *
     *     # Create content blocker
     *     $ wp borlabs-cookie content-blocker create some-content-blocker en "Some content blocker"
     *     Success: Created content blocker 93
     *
     *     # Create content blocker without success message
     *     $ wp borlabs-cookie content-blocker create some-content-blocker en "Some content blocker" --porcelain
     *     93
     */
    public function create(array $args, array $assocArgs): void
    {
        $key = $args[0];
        $language = $args[1];
        $name = $args[2];

        $status = (bool) (WP_CLI\Utils\get_flag_value($assocArgs, 'status', true));
        $undeletable = (bool) (WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', false));
        $previewHtml = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-html', '');
        $previewCss = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-css', '');
        $globalJs = WP_CLI\Utils\get_flag_value($assocArgs, 'global-js', '');
        $initJs = WP_CLI\Utils\get_flag_value($assocArgs, 'init-js', '');
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', '');
        $privacyPolicyUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'privacy-policy-url', '');
        $hosts = Sanitizer::hostArray(json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'hosts', '[]')));
        $executeGlobalCodeBeforeUnblocking = (bool) (
            WP_CLI\Utils\get_flag_value($assocArgs, 'execute-global-code-before-unblocking', false)
        );
        $unblockAll = (bool) (WP_CLI\Utils\get_flag_value($assocArgs, 'unblock-all', false));

        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $contentBlocker = new ContentBlockerModel(
            -1,
            $key,
            $language,
            $name,
            $description,
            $privacyPolicyUrl,
            $hosts,
            $previewHtml,
            $previewCss,
            $globalJs,
            $initJs,
            [
                'unblockAll' => $unblockAll,
                'executeGlobalCodeBeforeUnblocking' => $executeGlobalCodeBeforeUnblocking,
            ],
            $status,
            $undeletable,
        );
        $this->contentBlockerRepository->insert($contentBlocker);

        if ($porcelain) {
            WP_CLI::line($contentBlocker->id);
        } else {
            WP_CLI::success('Created content blocker ' . $contentBlocker->id);
        }
    }

    /**
     * Deletes one content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete content blocker 2
     *     $ wp borlabs-cookie content-blocker delete 2
     *     Success: Removed content blocker 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($id);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $id, true);

            return;
        }

        if ($contentBlocker->undeletable) {
            WP_CLI::error('The content blocker with id=' . $contentBlocker->id . ' is undeletable', true);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the content blocker id=' . $contentBlocker->id . '',
            $assocArgs,
        );

        $this->contentBlockerRepository->delete($contentBlocker);

        WP_CLI::success('Removed content blocker ' . $contentBlocker->id);
    }

    /**
     * Get details about a content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : Content blocker id
     *
     * [--field=<field>]
     * : Instead of returning the whole content blocker, returns the value of a single field.
     * ---
     * options:
     *   - id
     *   - content-blocker-id
     *   - language
     *   - name
     *   - privacy-policy-url
     *   - hosts
     *   - status
     *   - undeletable
     *   - description
     *   - preview-html
     *   - preview-css
     *   - global-js
     *   - init-js
     *   - unblock-all
     *   - execute-global-code-before-unblocking
     * ---
     *
     * [--fields=<fields>]
     * : Get a specific subset of the content blocker's fields.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields will be displayed by default for a content blocker:
     *
     * * id
     * * content-blocker-id
     * * language
     * * name
     * * privacy-policy-url
     * * hosts
     * * status
     * * undeletable
     *
     * These fields are optionally available:
     *
     * * description
     * * preview-html
     * * preview-css
     * * global-js
     * * init-js
     * * unblock-all
     * * execute-global-code-before-unblocking
     *
     * ## EXAMPLES
     *
     *     # Get content blocker
     *     $ wp borlabs-cookie content-blocker get 2 --field=name
     *     Default
     *
     *     # Get content blocker and export to JSON file
     *     $ wp borlabs-cookie content-blocker get 2 --format=json > contentBlocker.json
     */
    public function get(array $args, array $assocArgs): void
    {
        $contentBlocker = $this->contentBlockerRepository->findById((int) ($args[0]));

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $args[0], true);

            return;
        }

        $data = $this->mapToCliTable($contentBlocker);

        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($data);
    }

    /**
     * Gets a list of content blockers.
     *
     * ## OPTIONS
     *
     * [--field=<field>]
     * : Prints the value of a single field for each content blocker.
     * ---
     * options:
     *   - id
     *   - content-blocker-id
     *   - language
     *   - name
     *   - privacy-policy-url
     *   - hosts
     *   - status
     *   - undeletable
     *   - description
     *   - preview-html
     *   - preview-css
     *   - global-js
     *   - init-js
     *   - unblock-all
     *   - execute-global-code-before-unblocking
     * ---
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--orderby=<orderby>]
     * : Order the list by an attribute.
     * ---
     * default: id
     * options:
     *   - id
     *   - content-blocker-id
     *   - language
     *   - name
     *   - privacy-policy-url
     *   - status
     *   - undeletable
     * ---
     *
     * [--order=<order>]
     * : Order
     * ---
     * default: asc
     * options:
     *   - desc
     *   - asc
     * ---
     *
     * [--id=<id>]
     * : Filter by id.
     *
     * [--content-blocker-id=<content-blocker-id>]
     * : Filter by content blocker id.
     *
     * [--language=<language>]
     * : Filter by language.
     *
     * [--name=<name>]
     * : Filter by name.
     *
     * [--privacy-policy-url=<privacy-policy-url>]
     * : Filter by privacy policy url.
     *
     * [--status=<status>]
     * : Filter by status.
     *
     * [--undeletable=<undeletable>]
     * : Filter by undeletable.
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * ## AVAILABLE FIELDS
     *
     * These fields will be displayed by default for each content blocker:
     *
     * * id
     * * content-blocker-id
     * * language
     * * name
     * * privacy-policy-url
     * * hosts
     * * status
     * * undeletable
     *
     * These fields are optionally available:
     *
     * * description
     * * preview-html
     * * preview-css
     * * global-js
     * * init-js
     * * unblock-all
     * * execute-global-code-before-unblocking
     *
     * ## EXAMPLES
     *
     *     # List the ids of all content blockers
     *     $ wp borlabs-cookie content-blocker list --field=id
     *     1
     *     3
     *     4
     *     5
     *
     *     # List one field of content blockers in JSON
     *     $ wp borlabs-cookie content-blocker list --field=id --format=json
     *     [94,95,96,97,98,99,100,101]
     *
     *     # List all active content blockers in a table
     *     $ wp borlabs-cookie content-blocker list --status=1 --fields=id,name
     *     +----+---------------+
     *     | id | name          |
     *     +----+---------------+
     *     | 2  | Default       |
     *     | 1  | Facebook      |
     *     | 3  | Google Maps   |
     *     | 4  | Instagram     |
     *     | 5  | OpenStreetMap |
     *     | 6  | Twitter       |
     *     | 7  | Vimeo         |
     *     | 8  | YouTube       |
     *     +----+---------------+
     */
    public function list(array $args, array $assocArgs): void
    {
        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);

        $defaults = [];
        $filters = array_intersect_key(array_merge($defaults, $assocArgs), array_flip($this->orderAndFilterableFields));

        foreach ($this->fieldMap as $cliField => $modelField) {
            if (isset($filters[$cliField])) {
                $filters[$modelField] = $filters[$cliField];
                unset($filters[$cliField]);
            }
        }

        if ($assocArgs['orderby'] && in_array($assocArgs['orderby'], $this->orderAndFilterableFields, true)) {
            if (isset($this->fieldMap[$assocArgs['orderby']])) {
                $orderby = $this->fieldMap[$assocArgs['orderby']];
            } else {
                $orderby = $assocArgs['orderby'];
            }
        } else {
            $orderby = 'id';
        }

        if ($assocArgs['order'] && in_array(strtolower($assocArgs['order']), ['ASC', 'DESC'], true)) {
            $order = strtolower($assocArgs['order']);
        } else {
            $order = 'ASC';
        }
        $contentBlockers = $this->contentBlockerRepository->find(
            $filters,
            [
                $orderby => $order,
            ],
            [],
            true,
        );

        $iterator = WP_CLI\Utils\iterator_map(
            $contentBlockers,
            function (ContentBlockerModel $contentBlockerModel) {
                return $this->mapToCliTable($contentBlockerModel);
            },
        );

        $formatter->display_items($iterator);
    }

    /**
     * Reset default content blockers.
     *
     * ## EXAMPLES
     *
     *     # Reset default content blockers
     *     $ wp borlabs-cookie content-blocker reset
     *     Success: Reset was successful
     */
    public function reset(array $args, array $assocArgs): void
    {
        $success = $this->contentBlockerService->reset();

        if ($success) {
            WP_CLI::success('Reset was successful');
        } else {
            WP_CLI::error('Reset failed');
        }
    }

    /**
     * Update an existing content blocker.
     *
     * ## OPTIONS
     *
     * <contentBlocker>
     * : The id of the content blocker to update.
     *
     * [--key=<key>]
     * : The key of the content blocker.
     *
     * [--language=<language>]
     * : The language code (f.e. en, de, ...) of the content blocker.
     *
     * [--name=<name>]
     * : The name of the content blocker.
     *
     * [--status=<status>]
     * : Whether or not the content blocker will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether or not the content blocker will be undeletable.
     *
     * [--preview-html=<preview-html>]
     * : The preview HTML of the content blocker.
     *
     * [--preview-css=<preview-css>]
     * : The preview CSS of the content blocker.
     *
     * [--global-js=<global-js>]
     * : The global JS of the content blocker.
     *
     * [--init-js=<init-js>]
     * : The init JS of the content blocker.
     *
     * [--description=<description>]
     * : The description of the content blocker.
     *
     * [--privacy-policy-url=<privacy-policy-url>]
     * : The privacy policy URL of the content blocker.
     *
     * [--execute-global-code-before-unblocking=<execute-global-code-before-unblocking>]
     * : Whether or not the global code will be executed before unblocking.
     *
     * [--unblock-all=<unblock-all>]
     * : Whether or not unblocking one blocked content unblocks all other on the same page.
     *
     * [--porcelain]
     * : Output just the new content blocker id.
     *
     * ## EXAMPLES
     *
     *     # Update name of content blocker
     *     $ wp borlabs-cookie content-blocker update 93 --description="This is a content blocker x."
     *     Success: Updated content blocker 93.
     */
    public function update(array $args, array $assocArgs): void
    {
        $contentBlockerId = (int) ($args[0]);
        $contentBlocker = $this->contentBlockerRepository->findById($contentBlockerId);

        if ($contentBlocker === null) {
            WP_CLI::error('Cannot find content blocker with id=' . $contentBlockerId);

            return;
        }

        $key = WP_CLI\Utils\get_flag_value($assocArgs, 'key', null);

        if ($key !== null) {
            $contentBlocker->key = $key;
        }
        $language = WP_CLI\Utils\get_flag_value($assocArgs, 'language', null);

        if ($language !== null) {
            $contentBlocker->language = $language;
        }
        $name = WP_CLI\Utils\get_flag_value($assocArgs, 'name', null);

        if ($name !== null) {
            $contentBlocker->name = $name;
        }
        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status', null);

        if ($status !== null) {
            $contentBlocker->status = $status;
        }
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', null);

        if ($undeletable !== null) {
            $contentBlocker->undeletable = $undeletable;
        }
        $previewHtml = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-html', null);

        if ($previewHtml !== null) {
            $contentBlocker->previewHtml = $previewHtml;
        }
        $previewCss = WP_CLI\Utils\get_flag_value($assocArgs, 'preview-css', null);

        if ($previewCss !== null) {
            $contentBlocker->previewCss = $previewCss;
        }
        $globalJs = WP_CLI\Utils\get_flag_value($assocArgs, 'global-js', null);

        if ($globalJs !== null) {
            $contentBlocker->javaScriptGlobal = $globalJs;
        }
        $initJs = WP_CLI\Utils\get_flag_value($assocArgs, 'init-js', null);

        if ($initJs !== null) {
            $contentBlocker->javaScriptInitialization = $initJs;
        }
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', null);

        if ($description !== null) {
            $contentBlocker->description = $description;
        }
        $privacyPolicyUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'privacy-policy-url', null);

        if ($privacyPolicyUrl !== null) {
            $contentBlocker->privacyPolicyUrl = $privacyPolicyUrl;
        }
        $executeGlobalCodeBeforeUnblocking = WP_CLI\Utils\get_flag_value(
            $assocArgs,
            'execute-global-code-before-unblocking',
            null,
        );

        if ($executeGlobalCodeBeforeUnblocking !== null) {
            $contentBlocker->settingsFields['executeGlobalCodeBeforeUnblocking'] = (bool) $executeGlobalCodeBeforeUnblocking;
        }
        $unblockAll = WP_CLI\Utils\get_flag_value($assocArgs, 'unblock-all', null);

        if ($unblockAll !== null) {
            $contentBlocker->settingsFields['unblockAll'] = (bool) $unblockAll;
        }

        $success = $this->contentBlockerRepository->update($contentBlocker);

        if ($success) {
            WP_CLI::success('Updated content blocker ' . $contentBlocker->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    private function mapToCliTable(ContentBlockerModel $contentBlockerModel): array
    {
        return [
            'id' => $contentBlockerModel->id,
            'content-blocker-id' => $contentBlockerModel->key,
            'language' => $contentBlockerModel->language,
            'name' => $contentBlockerModel->name,
            'privacy-policy-url' => $contentBlockerModel->privacyPolicyUrl,
            'hosts' => $contentBlockerModel->hosts,
            'status' => $contentBlockerModel->status,
            'undeletable' => $contentBlockerModel->undeletable,
            // optional
            'description' => $contentBlockerModel->description,
            'preview-html' => $contentBlockerModel->previewHtml,
            'preview-css' => $contentBlockerModel->previewCss,
            'global-js' => $contentBlockerModel->javaScriptGlobal,
            'init-js' => $contentBlockerModel->javaScriptInitialization,
            'unblock-all' => $contentBlockerModel->settingsFields['unblockAll'] ?? false,
            'execute-global-code-before-unblocking' => $contentBlockerModel->settingsFields['executeGlobalCodeBeforeUnblocking']
                ?? false,
        ];
    }
}
