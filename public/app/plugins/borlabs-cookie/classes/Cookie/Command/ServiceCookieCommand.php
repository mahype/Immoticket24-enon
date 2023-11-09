<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\Service\ServiceCookieModel;
use Borlabs\Cookie\Repository\Service\ServiceCookieRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use WP_CLI;

/**
 * Lists, creates and deletes the service cookies of the Borlabs Cookie plugin.
 */
class ServiceCookieCommand extends AbstractCommand
{
    /**
     * @const FIELDS Default fields to display for each object.
     */
    public const FIELDS = [
        'id',
        'name',
        'lifetime',
        'type',
        'purpose',
    ];

    /**
     * @var string[] Map that defines which attributes map to which model property.
     *               If an attribute is not listed in this map, it is assumed that attribute and model property are the equal.
     */
    protected $fieldMap = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var ServiceCookieRepository
     */
    private $serviceCookieRepository;

    /**
     * @var ServiceRepository
     */
    private $serviceRepository;

    /**
     * ServiceCommand constructor.
     */
    public function __construct()
    {
        $this->container = ApplicationContainer::get();
        $this->serviceRepository = $this->container->get(ServiceRepository::class);
        $this->serviceCookieRepository = $this->container->get(ServiceCookieRepository::class);
    }

    /**
     * Creates a new service cookie.
     *
     * ## OPTIONS
     *
     * <serviceId>
     * : The key of the service.
     *
     * <name>
     * : The name of the service cookie.
     *
     * <lifetime>
     * : The lifetime of the service cookie.
     *
     * <type>
     * : The type of the service cookie.
     * ---
     * options:
     *   - http
     *   - session_storage
     *   - local_storage
     * ---
     *
     * <purpose>
     * : The purpose of the service cookie.
     * ---
     * options:
     *   - tracking
     *   - functional
     * ---
     *
     * [--porcelain]
     * : Output just the new service id.
     *
     * ## EXAMPLES
     *
     *     # Create service
     *     $ wp borlabs-cookie service-cookie create 23 cookie_name_1 "1 month" session_storage tracking
     *     Success: Created service cookie 244
     */
    public function create(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $name = $args[1];
        $lifetime = $args[2];
        $type = $args[3];
        $purpose = $args[4];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $service = $this->serviceRepository->findById($serviceId, false);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }

        $serviceCookie = new ServiceCookieModel(
            -1,
            $serviceId,
            $name,
            $lifetime,
            $type,
            $purpose,
        );

        $this->serviceCookieRepository->insert($serviceCookie);

        if ($porcelain) {
            WP_CLI::line($serviceCookie->id);
        } else {
            WP_CLI::success('Created service cookie ' . $serviceCookie->id);
        }
    }

    /**
     * Deletes one service cookie.
     *
     * ## OPTIONS
     *
     * <serviceCookie>
     * : The id of the service cookie to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service 244
     *     $ wp borlabs-cookie service-cookie delete 244 --yes
     *     Success: Removed service cookie 244
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $serviceCookie = $this->serviceCookieRepository->findById($id);

        if ($serviceCookie === null) {
            WP_CLI::error('Cannot find service cookie with id=' . $id, true);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service cookie id=' . $serviceCookie->id . '',
            $assocArgs,
        );

        $this->serviceCookieRepository->delete($serviceCookie);

        WP_CLI::success('Removed service cookie ' . $serviceCookie->id);
    }

    /**
     * Gets a list of service cookies.
     *
     * <service>
     * : Service id
     *
     * ## OPTIONS
     *
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
     *     # List the ids of all service cookies of one service
     *     $ wp borlabs-cookie service-cookie list 1
     */
    public function list(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $formatter = $this->getFormatter(
            $assocArgs,
            self::FIELDS,
        );

        $service = $this->serviceRepository->findById($serviceId, true);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }
        $serviceCookies = $service->serviceCookies;

        $iterator = \WP_CLI\Utils\iterator_map(
            $serviceCookies,
            function (ServiceCookieModel $serviceCookieModel) {
                foreach ($this->fieldMap as $cliField => $modelField) {
                    $serviceCookieModel->{$cliField} = $serviceCookieModel->{$modelField};
                    unset($serviceCookieModel->{$modelField});
                }

                return $serviceCookieModel;
            },
        );

        $formatter->display_items($iterator);
    }
}
