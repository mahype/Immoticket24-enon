<?php

declare(strict_types=1);

namespace Borlabs\Cookie\Command;

use Borlabs\Cookie\Container\ApplicationContainer;
use Borlabs\Cookie\Container\Container;
use Borlabs\Cookie\Model\Service\ServiceLocationModel;
use Borlabs\Cookie\Repository\Service\ServiceLocationRepository;
use Borlabs\Cookie\Repository\Service\ServiceRepository;
use WP_CLI;

/**
 * Lists, creates and deletes the service hosts of the Borlabs Cookie plugin.
 */
class ServiceLocationCommand extends AbstractCommand
{
    /**
     * @const FIELDS Default fields to display for each object.
     */
    public const FIELDS = [
        'id',
        'host',
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
     * @var ServiceLocationRepository
     */
    private $serviceLocationRepository;

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
        $this->serviceLocationRepository = $this->container->get(ServiceLocationRepository::class);
    }

    /**
     * Creates a new service host.
     *
     * ## OPTIONS
     *
     * <serviceId>
     * : The key of the service.
     *
     * <host>
     * : The host name of the service host.
     *
     *
     * [--porcelain]
     * : Output just the new service id.
     *
     * ## EXAMPLES
     *
     *     # Create service
     *     $ wp borlabs-cookie service-host create 3 some-site.com
     *     Success: Created service 225.
     */
    public function create(array $args, array $assocArgs): void
    {
        $serviceId = (int) ($args[0]);
        $host = $args[1];
        $porcelain = WP_CLI\Utils\get_flag_value($assocArgs, 'porcelain', false);

        $service = $this->serviceRepository->findById($serviceId, false);

        if ($service === null) {
            WP_CLI::error('Service with id=' . $serviceId . ' does not exist');

            return;
        }

        $serviceLocation = new ServiceLocationModel(
            -1,
            $serviceId,
            $host,
        );

        $this->serviceLocationRepository->insert($serviceLocation);

        if ($porcelain) {
            WP_CLI::line($serviceLocation->id);
        } else {
            WP_CLI::success('Created service location ' . $serviceLocation->id);
        }
    }

    /**
     * Deletes one service host.
     *
     * ## OPTIONS
     *
     * <serviceLocation>
     * : The id of the service host to delete.
     *
     * [--yes]
     * : Answer yes to any confirmation prompts.
     *
     * ## EXAMPLES
     *
     *     # Delete service host with ID 2
     *     $ wp borlabs-cookie service-host delete 2
     *     Success: Removed service host 2
     */
    public function delete(array $args, array $assocArgs): void
    {
        $id = (int) ($args[0]);
        $serviceLocation = $this->serviceLocationRepository->findById($id);

        if ($serviceLocation === null) {
            WP_CLI::error('Cannot find service location with id=' . $id, true);

            return;
        }
        WP_CLI::confirm(
            'Are you sure you want to delete the service location id=' . $serviceLocation->id . '',
            $assocArgs,
        );

        $this->serviceLocationRepository->delete($serviceLocation);

        WP_CLI::success('Removed service location ' . $serviceLocation->id);
    }

    /**
     * Gets a list of service hosts.
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
     *     $ wp borlabs-cookie service-host list 20
     *     +-----+---------------+
     *     | id  | host          |
     *     +-----+---------------+
     *     | 222 | www.vimeo.com |
     *     +-----+---------------+
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
        $serviceLocations = $service->serviceLocations;

        $iterator = \WP_CLI\Utils\iterator_map(
            $serviceLocations,
            function (ServiceLocationModel $serviceLocationModel) {
                foreach ($this->fieldMap as $cliField => $modelField) {
                    $serviceLocationModel->{$cliField} = $serviceLocationModel->{$modelField};
                    unset($serviceLocationModel->{$modelField});
                }

                return $serviceLocationModel;
            },
        );

        $formatter->display_items($iterator);
    }
}
