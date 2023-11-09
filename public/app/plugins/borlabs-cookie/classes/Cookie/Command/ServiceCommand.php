<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Enum\Service\DataCollectionEnum;
use Borlabs\Cookie\Enum\Service\DataPurposeEnum;
use Borlabs\Cookie\Enum\Service\DistributionEnum;
use Borlabs\Cookie\Enum\Service\LegalBasisEnum;
use Borlabs\Cookie\Enum\Service\LocationProcessingEnum;
use Borlabs\Cookie\Enum\Service\TechnologyEnum;
use Borlabs\Cookie\Model\Service\ServiceModel;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use Borlabs\Cookie\Support\Sanitizer;
use Borlabs\Cookie\System\License\License;
use Borlabs\Cookie\System\Message\MessageManager;
use Borlabs\Cookie\System\Service\DefaultService;
use Borlabs\Cookie\System\Service\ServiceApiSyncService;
use Borlabs\Cookie\System\Service\ServiceService;
use Borlabs\Cookie\Validator\Service\ServiceValidator;
use DateTime;
use WP_CLI;

/**
 * Lists, creates, updates and deletes the services of the Borlabs Cookie plugin.
 */
class ServiceCommand extends AbstractCommand
{
    /**
     * @const DEFAULT_FIELDS Default fields to display for each object.
     */
    public const DEFAULT_FIELDS = [
        'id',
        'name',
        'key',
        'provider-key',
        'provider-name',
        'language',
        'address',
        'partners',
        'description',
        'position',
        'undeletable',
        'status',
    ];

    /**
     * @const OPTIONAL_FIELDS Optional field to display for each object.
     */
    public const OPTIONAL_FIELDS = [
        'data-purpose',
        'technology',
        'data-collection',
        'legal-basis',
        'location-processing',
        'distribution',
        'privacy-url',
        'cookie-url',
        'opt-out-url',
        'opt-in-code',
        'opt-out-code',
        'fallback-code',
        'last-synced-at',
        // settings
        'block-cookies-before-consent',
        'prioritize',
    ];

    /**
     * @const PROVIDER_FIELDS Fields to display for each provider.
     */
    public const PROVIDER_FIELDS = [
        'key',
        'name',
        'description',
    ];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected $fieldMap = [
        'key' => 'serviceId',
        'provider-key' => 'providerKey',
        'provider-name' => 'providerName',
        'privacy-url' => 'privacyUrl',
        'cookie-url' => 'cookieUrl',
        'opt-out-url' => 'optOutUrl',
    ];

    /**
     * @var string[] list of attributes that can be filtered / ordered with wp cli
     */
    protected $orderAndFilterableFields = [
        'id',
        'name',
        'key',
        'provider-key',
        'provider-name',
        'language',
        'address',
        'partners',
        'description',
        'privacy-url',
        'cookie-url',
        'opt-out-url',
        'position',
        'undeletable',
        'status',
    ];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var License
     */
    private $license;

    /**
     * @var MessageManager
     */
    private $message;

    /**
     * @var ServiceApiSyncService
     */
    private $serviceApiSyncService;

    /**
     * @var ServiceRepository
     */
    private $serviceRepository;

    /**
     * @var ServiceService
     */
    private $serviceService;

    /**
     * @var ServiceValidator
     */
    private $serviceValidation;

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceRepository = $this->container->get(ServiceRepository::class);
        $this->serviceApiSyncService = $this->container->get(ServiceApiSyncService::class);
        $this->serviceService = $this->container->get(ServiceService::class);
        $this->serviceValidation = $this->container->get(ServiceValidator::class);
        $this->message = $this->container->get(MessageManager::class);
        $this->license = $this->container->get(License::class);
    }

    /**
     * Creates a new service.
     *
     * ## OPTIONS
     *
     * <key>
     * : The key of the service.
     *
     * <language>
     * : The language code (f.e. en, de, ...) of the service.
     *
     * <name>
     * : The name of the service.
     *
     * <position>
     * : The position of the service.
     *
     * <service-group-id>
     * : The ID of the service group the service should belong to.
     *
     * <provider-name>
     * : The provider name of the service.
     *
     * [--description=<description>]
     * : The description of the service.
     *
     * [--partners=<partners>]
     * : The partners of the service.
     *
     * [--address=<address>]
     * : The address of the service.
     *
     * [--privacy-url=<privacy-url>]
     * : The privacy url of the service.
     *
     * [--cookie-url=<cookie-url>]
     * : The cookie url of the service.
     *
     * [--opt-out-url=<opt-out-url>]
     * : The opt out url of the service.
     *
     * [--data-purpose=<data-purpose>]
     * : The array of data purpose options.
     * Run `wp borlabs-cookie service list-options data-purpose` to get a list of available options.
     *
     * [--technology=<technology>]
     * : The array of technology options.
     * Run `wp borlabs-cookie service list-options technology` to get a list of available options.
     *
     * [--data-collection=<data-collection>]
     * : The array of data collection options.
     * Run `wp borlabs-cookie service list-options data-collection` to get a list of available options.
     *
     * [--legal-basis=<legal-basis>]
     * : The array of legal basis options.
     * Run `wp borlabs-cookie service list-options legal-basis` to get a list of available options.
     *
     * [--location-processing=<location-processing>]
     * : The array of location processing options.
     * Run `wp borlabs-cookie service list-options location-processing` to get a list of available options.
     *
     * [--distribution=<distribution>]
     * : The array of distribution options.
     * Run `wp borlabs-cookie service list-options distribution` to get a list of available options.
     *
     * [--block-cookies-before-consent=<block-cookies-before-consent>]
     * : Whether or not the service will block cookies before the consent.
     *
     * [--prioritize=<prioritize>]
     * : Whether or not the service will be prioritized.
     *
     * [--opt-in-code=<opt-in-code>]
     * : The opt in code of the service.
     *
     * [--opt-out-code=<opt-out-code>]
     * : The opt out code of the service.
     *
     * [--fallback-code=<fallback-code>]
     * : The fallback code of the service.
     *
     * [--status=<status>]
     * : Whether or not the service will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether or not the service will be undeletable.
     *
     * [--porcelain]
     * : Output just the new service id.
     *
     * ## EXAMPLES
     *
     *     # Create service
     *     $ wp borlabs-cookie service create some-service en "Some Service" 10 70 "A Provider"
     *     Success: Created service 93
     *
     *     # Create service without success message
     *     $ wp borlabs-cookie service create some-service en "Some Service" 10 70 "A Provider" --porcelain
     *     93
     */
    public function create(array $args, array $assocArgs): void
    {
        $key = $args[0];
        $language = $args[1];
        $name = $args[2];
        $position = $args[3];
        $serviceGroupId = (int) ($args[4]);
        $providerName = $args[5];
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', '');
        $partners = WP_CLI\Utils\get_flag_value($assocArgs, 'partners', '');
        $address = WP_CLI\Utils\get_flag_value($assocArgs, 'address', '');
        $privacyUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'privacy-url', '');
        $cookieUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-url', '');
        $optOutUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-out-url', '');
        $blockCookiesBeforeConsent = WP_CLI\Utils\get_flag_value($assocArgs, 'block-cookies-before-consent', false);
        $prioritize = WP_CLI\Utils\get_flag_value($assocArgs, 'prioritize', false);
        $optInCode = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-in-code', '');
        $optOutCode = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-out-code', '');
        $fallbackCode = WP_CLI\Utils\get_flag_value($assocArgs, 'fallback-code', '');
        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status', true);
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', false);
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $dataPurpose = Sanitizer::enumList(
            json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'data-purpose', '[]')),
            DataPurposeEnum::class,
        );
        $technology = Sanitizer::enumList(
            json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'technology', '[]')),
            TechnologyEnum::class,
        );
        $dataCollection = Sanitizer::enumList(
            json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'data-collection', '[]')),
            DataCollectionEnum::class,
        );
        $legalBasis = Sanitizer::enumList(
            json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'legal-basis', '[]')),
            LegalBasisEnum::class,
        );
        $locationProcessing = Sanitizer::enumList(
            json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'location-processing', '[]')),
            LocationProcessingEnum::class,
        );
        $distribution = Sanitizer::enumList(
            json_decode(WP_CLI\Utils\get_flag_value($assocArgs, 'distribution', '[]')),
            DistributionEnum::class,
        );

        $validationData = [
            'id' => '-1',
            'serviceId' => $key,
            'name' => $name,
            'providerName' => $providerName,
        ];

        $service = new ServiceModel(
            -1,
            $key,
            null,
            $language,
            $serviceGroupId,
            $name,
            $address,
            $providerName,
            $partners,
            $description,
            $privacyUrl,
            $cookieUrl,
            $optOutUrl,
            $dataPurpose,
            $technology,
            $dataCollection,
            $legalBasis,
            $locationProcessing,
            $distribution,
            $optInCode,
            $optOutCode,
            $fallbackCode,
            [
                'blockCookiesBeforeConsent' => (bool) $blockCookiesBeforeConsent ? '1' : '0',
                'prioritize' => (bool) $prioritize ? '1' : '0',
            ],
            (int) $position,
            (bool) $status,
            (bool) $undeletable,
            null,
            [],
            [],
        );

        if (!$this->serviceValidation->isValid($validationData)) {
            $this->printMessages($this->message->getRaw());
            WP_CLI::error('Service creation failed');

            return;
        }

        $this->serviceRepository->insert($service);

        if ($porcelain) {
            WP_CLI::line($service->id);
        } else {
            WP_CLI::success('Created service ' . $service->id);
        }
    }

    /**
     * Creates a new external service.
     *
     * ## OPTIONS
     *
     * <providerKey>
     * : The key of the provider.
     * Use `wp borlabs-cookie list-providers` to get a list of possible provider keys.
     *
     * <key>
     * : The key of the service.
     *
     * <language>
     * : The language code (f.e. en, de, ...) of the service.
     *
     * <name>
     * : The name of the service.
     *
     * <service-group-id>
     * : The ID of the service group the service should belong to.
     *
     * <position>
     * : The position of the service.
     *
     * [--porcelain]
     * : Output just the new service id.
     *
     * ## EXAMPLES
     *
     *     # Create external service
     *     $ wp borlabs-cookie service create-external cloudflare cloudflare en Cloudflare 2 4
     *     Created service 123
     *
     *     # Create external service without success message
     *     $ wp borlabs-cookie service create-external cloudflare cloudflare en Cloudflare 2 4 --porcelain
     *     123
     *
     * @subcommand create-external
     */
    public function createExternal(array $args, array $assocArgs): void
    {
        // TODO: re-work this method
        WP_CLI::error('ProvidersApiClient was removed. This method needs an update');

        $providerKey = $args[0];
        $key = $args[1];
        $language = $args[2];
        $name = $args[3];
        $serviceGroupId = $args[4];
        $position = $args[5];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $validationData = [
            'id' => '-1',
            'serviceId' => $key,
            'name' => $name,
            'providerKey' => $providerKey,
        ];

        if (!$this->serviceValidation->isValid($validationData)) {
            $this->printMessages($this->message->getRaw());
            WP_CLI::error('Service creation failed');

            return;
        }

        /** @var DefaultService $defaultService */
        $defaultService = $this->container->get(DefaultService::class);
        $service = $defaultService->get();

        if ($this->license->isPluginUnlocked() && $this->license->isLicenseValid()) {
            $provider = $this->providers->requestProvider($providerKey);

            if ($provider === null) {
                WP_CLI::error('ApiClient not reachable.');

                return;
            }

            if (!$provider->found) {
                WP_CLI::error('Provider key not found.');

                return;
            }
            $service = $this->serviceApiSyncService->convertProviderApiToServiceModel(
                $service,
                $provider,
            );
        } else {
            WP_CLI::error('Please renew your license to use the provider ApiClient.');

            return;
        }

        $service->lastSyncedAt = new DateTime();
        $service->serviceId = $key;
        $service->serviceGroupId = $serviceGroupId;
        $service->position = $position;
        $service->name = $name;
        $service->language = $language;
        $service->partners = $service->partners === null ? '' : $service->partners;

        $this->serviceRepository->insert($service);

        if ($porcelain) {
            WP_CLI::line($service->id);
        } else {
            WP_CLI::success('Created external service ' . $service->id);
        }
    }

    /**
     * Deletes one service.
     *
     * ## OPTIONS
     *
     * <serviceGroup>
     * : The id of the service to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service 2
     *     $ wp borlabs-cookie service delete 2
     *     Success: Removed service 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $service = $this->serviceRepository->findById($id);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $id, true);

            return;
        }

        if ($service->undeletable) {
            WP_CLI::error('The service with id=' . $service->id . ' is undeletable', true);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service "' . $service->name . '" in language "' . $service->language
            . '" ',
            $assocArgs,
        );

        $this->serviceRepository->delete($service);

        WP_CLI::success('Removed service ' . $service->id);
    }

    /**
     * Get details about a service.
     *
     * ## OPTIONS
     *
     * <service>
     * : Service id
     *
     * [--field=<field>]
     * : Instead of returning the whole service, returns the value of a single field.
     * ---
     * options:
     *   - id
     *   - name
     *   - key
     *   - provider-key
     *   - provider-name
     *   - language
     *   - address
     *   - partners
     *   - description
     *   - data-purpose
     *   - technology
     *   - data-collection
     *   - legal-basis
     *   - location-processing
     *   - distribution
     *   - position
     *   - undeletable
     *   - status
     *   - privacy-url
     *   - cookie-url
     *   - opt-out-url
     *   - opt-in-code
     *   - opt-out-code
     *   - fallback-code
     *   - last-synced-at
     *   - block-cookies-before-consent
     *   - prioritize
     * ---
     *
     * [--fields=<fields>]
     * : Get a specific subset of the service's fields.
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
     * These fields will be displayed by default for the service:
     *
     * * id
     * * name
     * * key
     * * provider-key
     * * provider-name
     * * language
     * * address
     * * partners
     * * description
     * * position
     * * undeletable
     * * status
     *
     * These fields are optionally available:
     *
     * * data-purpose
     * * technology
     * * data-collection
     * * legal-basis
     * * location-processing
     * * distribution
     * * privacy-url
     * * cookie-url
     * * opt-out-url
     * * opt-in-code
     * * opt-out-code
     * * fallback-code
     * * last-synced-at
     * * block-cookies-before-consent
     * * prioritize
     *
     * ## EXAMPLES
     *
     *     # Get service
     *     $ wp borlabs-cookie service get 1 --field=name
     *     supervisor
     *
     *     # Get service and export to JSON file
     *     $ wp borlabs-cookie service get 1 --format=json > service.json
     */
    public function get(array $args, array $assocArgs): void
    {
        $service = $this->serviceRepository->findById((int) ($args[0]), true);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $args[0], true);

            return;
        }

        $data = $this->mapToCliTable($service);

        $formatter = $this->getFormatter($assocArgs, self::DEFAULT_FIELDS);
        $formatter->display_item($data);
    }

    /**
     * Gets a list of services.
     *
     * ## OPTIONS
     *
     * [--field=<field>]
     * : Prints the value of a single field for each service.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
     *
     * [--orderby=<orderby>]
     * : Order the list by an attribute.
     * ---
     * default: position
     * options:
     *    - id
     *    - name
     *    - key
     *    - provider-key
     *    - provider-name
     *    - language
     *    - address
     *    - partners
     *    - description
     *    - privacy-url
     *    - cookie-url
     *    - opt-out-url
     *    - position
     *    - undeletable
     *    - status
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
     * [--name=<name>]
     * : Filter by name.
     *
     * [--key=<key>]
     * : Filter by service id.
     *
     * [--provider-key=<provider-key>]
     * : Filter by provider key.
     *
     * [--provider-name=<provider-name>]
     * : Filter by provider name.
     *
     * [--language=<language>]
     * : Filter by language.
     *
     * [--address=<address>]
     * : Filter by address.
     *
     * [--partners=<partners>]
     * : Filter by partners.
     *
     * [--description=<description>]
     * : Filter by description.
     *
     * [--privacy-url=<privacy-url>]
     * : Filter by privacy URL.
     *
     * [--cookie-url=<cookie-url>]
     * : Filter by cookie URL.
     *
     * [--opt-out-url=<opt-out-url>]
     * : Filter by opt out URL.
     *
     * [--position=<position>]
     * : Filter by position.
     *
     * [--undeletable=<undeletable>]
     * : Filter by undeletable.
     *
     * [--status=<status>]
     * : Filter by status.
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
     * These fields will be displayed by default for each service:
     *
     * * id
     * * name
     * * key
     * * provider-key
     * * provider-name
     * * language
     * * address
     * * partners
     * * description
     * * position
     * * undeletable
     * * status
     *
     * These fields are optionally available:
     *
     * * data-purpose
     * * technology
     * * data-collection
     * * legal-basis
     * * location-processing
     * * distribution
     * * privacy-url
     * * cookie-url
     * * opt-out-url
     * * opt-in-code
     * * opt-out-code
     * * fallback-code
     * * last-synced-at
     * * block-cookies-before-consent
     * * prioritize
     *
     * ## EXAMPLES
     *
     *     # List the ids of all services
     *     $ wp borlabs-cookie service list --field=id
     *     1
     *     3
     *     4
     *     5
     *
     *     # List one field of services in JSON
     *     $ wp borlabs-cookie service list --field=id --format=json
     *     [94,95,96,97,98,99,100,101]
     *
     *     # List all services active services in table
     *     $ wp borlabs-cookie service list --status=1 --fields=id,name
     *     +-----+-------------------+
     *     | id  | name              |
     *     +-----+-------------------+
     *     | 94  | Borlabs Cookie    |
     *     | 95  | Facebook          |
     *     | 96  | Google Maps       |
     *     | 97  | Instagram         |
     *     | 98  | OpenStreetMap     |
     *     | 99  | Twitter           |
     *     | 100 | Vimeo             |
     *     | 101 | YouTube           |
     *     +-----+-------------------+
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
            $orderby = 'position';
        }

        if ($assocArgs['order'] && in_array(strtolower($assocArgs['order']), ['ASC', 'DESC'], true)) {
            $order = strtolower($assocArgs['order']);
        } else {
            $order = 'ASC';
        }
        $services = $this->serviceRepository->find(
            $filters,
            [
                $orderby => $order,
            ],
            [],
            true,
        );

        $iterator = WP_CLI\Utils\iterator_map($services, function (ServiceModel $service) {
            return $this->mapToCliTable($service);
        });

        $formatter->display_items($iterator);
    }

    /**
     * List options for a service type.
     *
     * ## OPTIONS
     *
     * <type>
     * : Name of the type.
     * ---
     * options:
     *   - data-purpose
     *   - technology
     *   - data-collection
     *   - legal-basis
     *   - location-processing
     *   - distribution
     * ---
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
     * ## EXAMPLES
     *
     *     # List options for type "purpose"
     *     $ wp borlabs-cookie service list-options data-purpose
     *     +-----+------------------------------------------------+
     *     | key | name                                           |
     *     +-----+------------------------------------------------+
     *     | 717 | Advertising                                    |
     *     | 718 | Analytics                                      |
     *     | 719 | Bot Protection                                 |
     *     | 720 | Compliance with legal obligations              |
     *     | 721 | Conversion tracking                            |
     *     | 722 | Conversions                                    |
     *     | 723 | Customer relationship management               |
     *     | 724 | Detecting code errors                          |
     *     | 725 | Displaying videos                              |
     *     | 726 | Email campaigns                                |
     *     | 727 | Error tracking                                 |
     *     | 728 | Facilitating online forms and surveys          |
     *     | 729 | Facilitating webinar sign-ups                  |
     *     | 730 | Functionality                                  |
     *     | 731 | Generating leads                               |
     *     | 732 | Job application                                |
     *     | 733 | Marketing                                      |
     *     | 734 | Monitoring system stability                    |
     *     | 735 | Online automation                              |
     *     | 736 | Online polls                                   |
     *     | 737 | Optimization                                   |
     *     | 738 | Payment                                        |
     *     | 739 | Personalization                                |
     *     | 740 | Providing targeted messages and communications |
     *     | 741 | Remarketing                                    |
     *     | 742 | Scheduling a demonstration                     |
     *     | 743 | Storage of Consent                             |
     *     | 744 | Targeting                                      |
     *     | 745 | Tracking                                       |
     *     | 746 | Web analytics                                  |
     *     +-----+------------------------------------------------+
     *
     * @subcommand list-options
     */
    public function listOptions(array $args, array $assocArgs): void
    {
        $formatter = $this->getFormatter($assocArgs, [
            'key',
            'name',
        ]);
        $option = $args[0];
        $options = [];

        if ($option === 'data-purpose') {
            $options = DataPurposeEnum::getAll();
        } elseif ($option === 'technology') {
            $options = TechnologyEnum::getAll();
        } elseif ($option === 'data-collection') {
            $options = DataCollectionEnum::getAll();
        } elseif ($option === 'legal-basis') {
            $options = LegalBasisEnum::getAll();
        } elseif ($option === 'location-processing') {
            $options = LocationProcessingEnum::getAll();
        } elseif ($option === 'distribution') {
            $options = LocationProcessingEnum::getAll();
        } else {
            WP_CLI::error('The option "' . $option . '" does not exist');
        }

        $iterator = WP_CLI\Utils\iterator_map($options, function (array $option) {
            return $option;
        });

        $formatter->display_items($iterator);
    }

    /**
     * List providers from provider ApiClient.
     *
     * [--field=<field>]
     * : Prints the value of a single field for each service.
     *
     * [--fields=<fields>]
     * : Limit the output to specific object fields.
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
     * ## EXAMPLES
     *
     *     # List providers
     *     $ wp borlabs-cookie service list-providers
     *     +---------------+---------------+------------------------------------------+
     *     | key           | name          | description                              |
     *     +---------------+---------------+------------------------------------------+
     *     | some-provider | Some provider | This is the best provider for analytics. |
     *     +---------------+---------------+------------------------------------------+
     *
     *     # List providers in json and only the key and name columns
     *     $ wp borlabs-cookie service list-providers --fields=key,name
     *     [{"key":"some-provider","name":"Some provider"}]
     *
     * @subcommand list-providers
     */
    public function listProviders(array $args, array $assocArgs): void
    {
        $formatter = $this->getFormatter($assocArgs, self::PROVIDER_FIELDS);

        $providers = [];

        if ($this->license->isPluginUnlocked() && $this->license->isLicenseValid()) {
            $apiProviders = $this->providers->requestProviderList();

            if ($apiProviders !== null) {
                $providers = $apiProviders;
            } else {
                WP_CLI::error('ApiClient not reachable.');
            }
        } else {
            WP_CLI::error('Please renew your license to use the provider ApiClient.');

            return;
        }
        $providers = apply_filters('borlabsCookie/cookie/service/selection', $providers);

        $iterator = WP_CLI\Utils\iterator_map($providers, function (object $provider) {
            return [
                'key' => $provider->provider_key,
                'name' => $provider->name,
                'description' => $provider->description,
            ];
        });

        $formatter->display_items($iterator);
    }

    /**
     * Reset default services.
     *
     * ## EXAMPLES
     *
     *     # Reset default services
     *     $ wp borlabs-cookie service reset
     *     Reset was successful
     */
    public function reset(array $args, array $assocArgs): void
    {
        $success = $this->serviceService->reset();

        if ($success) {
            WP_CLI::success('Reset was successful');
        } else {
            WP_CLI::error('Reset failed');
        }
    }

    /**
     * Sync one service with the ApiClient.
     *
     * ## OPTIONS
     *
     * <service>
     * : Service id
     *
     * ## EXAMPLES
     *
     *     # Sync service
     *     $ wp borlabs-cookie service sync 2
     *     Success: Sync was successful
     */
    public function sync(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);

        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Cannot find service with id=' . $args[0], true);

            return;
        }

        $success = $this->serviceApiSyncService->syncService($service);

        $this->printMessages($this->message->getRaw());

        if ($success) {
            WP_CLI::success('Sync was successful');
        } else {
            WP_CLI::error('Sync failed');
        }
    }

    /**
     * Sync all services with the ApiClient.
     *
     * ## EXAMPLES
     *
     *     # Sync services
     *     $ wp borlabs-cookie service sync-all
     *     Success: Sync was successful
     *
     * @subcommand sync-all
     */
    public function syncAll(array $args, array $assocArgs): void
    {
        $success = $this->serviceApiSyncService->syncAllServices();

        /** @var MessageManager $message */
        $message = $this->container->get(MessageManager::class);

        $this->printMessages($this->message->getRaw());

        if ($success) {
            WP_CLI::success('Sync was successful');
        } else {
            WP_CLI::error('Sync failed');
        }
    }

    /**
     * Update an existing service.
     *
     * ## OPTIONS
     *
     * <service>
     * : The id of the service to update.
     *
     * [--name=<name>]
     * : The name of the service.
     *
     * [--position=<position>]
     * : The position of the service.
     *
     * [--service-group-id=<service-group-id>]
     * : The ID of the service group the service should belong to.
     *
     * [--provider-name=<provider-name>]
     * : The provider name of the service.
     *
     * [--description=<description>]
     * : The description of the service.
     *
     * [--partners=<partners>]
     * : The partners of the service.
     *
     * [--address=<address>]
     * : The address of the service.
     *
     * [--privacy-url=<privacy-url>]
     * : The privacy url of the service.
     *
     * [--cookie-url=<cookie-url>]
     * : The cookie url of the service.
     *
     * [--opt-out-url=<opt-out-url>]
     * : The opt out url of the service.
     *
     * [--block-cookies-before-consent=<block-cookies-before-consent>]
     * : Whether or not the service will block cookies before the consent.
     *
     * [--prioritize=<prioritize>]
     * : Whether or not the service will be prioritized.
     *
     * [--opt-in-code=<opt-in-code>]
     * : The opt in code of the service.
     *
     * [--opt-out-code=<opt-out-code>]
     * : The opt out code of the service.
     *
     * [--fallback-code=<fallback-code>]
     * : The fallback code of the service.
     *
     * [--data-purpose=<data-purpose>]
     * : The array of data purpose options.
     * Run `wp borlabs-cookie service list-options data-purpose` to get a list of available options.
     *
     * [--technology=<technology>]
     * : The array of technology options.
     * Run `wp borlabs-cookie service list-options technology` to get a list of available options.
     *
     * [--data-collection=<data-collection>]
     * : The array of data collection options.
     * Run `wp borlabs-cookie service list-options data-collection` to get a list of available options.
     *
     * [--legal-basis=<legal-basis>]
     * : The array of legal basis options.
     * Run `wp borlabs-cookie service list-options legal-basis` to get a list of available options.
     *
     * [--location-processing=<location-processing>]
     * : The array of location processing options.
     * Run `wp borlabs-cookie service list-options location-processing` to get a list of available options.
     *
     * [--distribution=<distribution>]
     * : The array of distribution options.
     * Run `wp borlabs-cookie service list-options distribution` to get a list of available options.
     *
     * [--status=<status>]
     * : Whether or not the service will be activated.
     *
     * [--undeletable=<undeletable>]
     * : Whether or not the service will be undeletable.
     *
     * [--porcelain]
     * : Output just the new service id.
     *
     * ## EXAMPLES
     *
     *     # Update description of service
     *     $ wp borlabs-cookie service update 93 --description="This is a tracking service."
     *     Success: Updated service 93.
     *
     *     # Update data purposes of service
     *     $ wp borlabs-cookie service update 93 --data-purpose=[717,718]
     *     Success: Updated service 93.
     */
    public function update(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $service = $this->serviceRepository->findById($serviceId);

        if ($service === null) {
            WP_CLI::error('Cannot find service group with id=' . $serviceId);

            return;
        }

        $name = WP_CLI\Utils\get_flag_value($assocArgs, 'name', null);

        if ($name !== null) {
            $service->name = $name;
        }
        $position = WP_CLI\Utils\get_flag_value($assocArgs, 'position', null);

        if ($position !== null) {
            $service->position = $position;
        }
        $description = WP_CLI\Utils\get_flag_value($assocArgs, 'description', null);

        if ($description !== null) {
            $service->description = $description;
        }
        $status = WP_CLI\Utils\get_flag_value($assocArgs, 'status', null);

        if ($status !== null) {
            $service->status = $status;
        }
        $providerName = WP_CLI\Utils\get_flag_value($assocArgs, 'provider-name', null);

        if ($providerName !== null) {
            $service->providerName = $providerName;
        }
        $partners = WP_CLI\Utils\get_flag_value($assocArgs, 'partners', null);

        if ($partners !== null) {
            $service->partners = $partners;
        }
        $address = WP_CLI\Utils\get_flag_value($assocArgs, 'address', null);

        if ($address !== null) {
            $service->address = $address;
        }
        $privacyUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'privacy-url', null);

        if ($privacyUrl !== null) {
            $service->privacyUrl = $privacyUrl;
        }
        $cookieUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'cookie-url', null);

        if ($cookieUrl !== null) {
            $service->cookieUrl = $cookieUrl;
        }
        $optOutUrl = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-out-url', null);

        if ($optOutUrl !== null) {
            $service->optOutUrl = $optOutUrl;
        }
        $blockCookiesBeforeConsent = WP_CLI\Utils\get_flag_value($assocArgs, 'block-cookies-before-consent', null);

        if ($optOutUrl !== null) {
            $service->settings['blockCookiesBeforeConsent'] = $blockCookiesBeforeConsent;
        }
        $prioritize = WP_CLI\Utils\get_flag_value($assocArgs, 'prioritize', null);

        if ($optOutUrl !== null) {
            $service->settings['prioritize'] = $prioritize;
        }
        $optInJavaScript = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-in-code', null);

        if ($optInJavaScript !== null) {
            $service->optInJavaScript = $optInJavaScript;
        }
        $optOutJavaScript = WP_CLI\Utils\get_flag_value($assocArgs, 'opt-out-code', null);

        if ($optOutJavaScript !== null) {
            $service->optOutJavaScript = $optOutJavaScript;
        }
        $fallbackJavaScript = WP_CLI\Utils\get_flag_value($assocArgs, 'fallback-code', null);

        if ($fallbackJavaScript !== null) {
            $service->fallbackJavaScript = $fallbackJavaScript;
        }
        $undeletable = WP_CLI\Utils\get_flag_value($assocArgs, 'undeletable', null);

        if ($undeletable !== null) {
            $service->undeletable = $undeletable;
        }
        $dataPurpose = WP_CLI\Utils\get_flag_value($assocArgs, 'data-purpose', null);

        if ($dataPurpose !== null) {
            $service->dataPurpose = Sanitizer::enumList(json_decode($dataPurpose), DataPurposeEnum::class);
        }
        $technology = WP_CLI\Utils\get_flag_value($assocArgs, 'technology', null);

        if ($technology !== null) {
            $service->technology = Sanitizer::enumList(json_decode($technology), TechnologyEnum::class);
        }
        $dataCollection = WP_CLI\Utils\get_flag_value($assocArgs, 'data-collection', null);

        if ($dataCollection) {
            $service->dataCollection = Sanitizer::enumList(json_decode($dataCollection), DataCollectionEnum::class);
        }
        $legalBasis = WP_CLI\Utils\get_flag_value($assocArgs, 'legal-basis', null);

        if ($legalBasis !== null) {
            $service->legalBasis = Sanitizer::enumList(json_decode($legalBasis), LegalBasisEnum::class);
        }
        $locationProcessing = WP_CLI\Utils\get_flag_value($assocArgs, 'location-processing', null);

        if ($locationProcessing !== null) {
            $service->locationProcessing = Sanitizer::enumList(
                json_decode($locationProcessing),
                LocationProcessingEnum::class,
            );
        }
        $distribution = WP_CLI\Utils\get_flag_value($assocArgs, 'distribution', null);

        if ($distribution !== null) {
            $service->distribution = Sanitizer::enumList(json_decode($distribution), DistributionEnum::class);
        }

        $validationData = [
            'id' => '' . $serviceId,
            'name' => $service->name,
            'providerKey' => $service->providerKey,
            'providerName' => $service->providerName,
        ];

        if (!$this->serviceValidation->isValid($validationData)) {
            $this->printMessages($this->message->getRaw());
            WP_CLI::error('Service creation failed');

            return;
        }

        $success = $this->serviceRepository->update($service);

        if ($success) {
            WP_CLI::success('Updated service ' . $service->id . '.');
        } else {
            WP_CLI::error('Update failed.');
        }
    }

    private function mapToCliTable(ServiceModel $serviceModel): array
    {
        return [
            'id' => $serviceModel->id,
            'name' => $serviceModel->name,
            'key' => $serviceModel->serviceId,
            'provider-key' => $serviceModel->providerKey,
            'provider-name' => $serviceModel->providerName,
            'language' => $serviceModel->language,
            'address' => $serviceModel->address,
            'partners' => $serviceModel->partners,
            'description' => $serviceModel->description,
            'position' => $serviceModel->position,
            'undeletable' => $serviceModel->undeletable,
            'status' => $serviceModel->status,
            // optional fields
            'data-purpose' => $serviceModel->dataPurpose,
            'technology' => $serviceModel->technology,
            'data-collection' => $serviceModel->dataCollection,
            'legal-basis' => $serviceModel->legalBasis,
            'location-processing' => $serviceModel->locationProcessing,
            'distribution' => $serviceModel->distribution,
            'privacy-url' => $serviceModel->privacyUrl,
            'cookie-url' => $serviceModel->cookieUrl,
            'opt-out-url' => $serviceModel->optOutUrl,
            'opt-in-code' => $serviceModel->optInJavaScript,
            'opt-out-code' => $serviceModel->optOutJavaScript,
            'fallback-code' => $serviceModel->fallbackJavaScript,
            'last-synced-at' => $serviceModel->lastSyncedAt !== null ? $serviceModel->lastSyncedAt->format('c') : null,
            // settings
            'block-cookies-before-consent' => $serviceModel->settings['block-cookies-before-consent'] ?? false,
            'prioritize' => $serviceModel->settings['prioritize'] ?? false,
        ];
    }
}
