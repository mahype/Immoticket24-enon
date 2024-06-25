<?php
/**
 * @license Apache-2.0
 *
 * Modified by borlabs on 04-June-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace Borlabs\Cookie\Dependencies\MaxMind\Db\Reader;

/**
 * This class should be thrown when unexpected data is found in the database.
 */
// phpcs:disable
class InvalidDatabaseException extends \Exception {}
