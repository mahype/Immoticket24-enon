<?php

namespace Enev\Schema202403\Calculations;

require dirname(__DIR__) . '/vendor/autoload.php';

use AWSM\LibEstate\Calculations\Building;
use AWSM\LibEstate\Calculations\Basement;
use AWSM\LibEstate\Calculations\Roof;
use AWSM\LibEstate\Calculations\Coolers;
use AWSM\LibEstate\Calculations\Heater;
use AWSM\LibEstate\Calculations\Heaters;
use AWSM\LibEstate\Calculations\HeaterSystem;
use AWSM\LibEstate\Calculations\HotWaterHeater;
use AWSM\LibEstate\Calculations\HotWaterHeaters;
use AWSM\LibEstate\Calculations\SolarHeater;
use AWSM\LibEstate\Helpers\TimePeriods;
use WPENON\Model\Energieausweis;

/**
 * Class CalculationsCC
 *
 * @since 1.0.0
 */
class CalculationsCC
{
	/**
	 * Energy certificate (Energy certificate)
	 *
	 * @var Energieausweis
	 *
	 * @since 1.0.0
	 */
	protected Energieausweis $ec;

	/**
	 * Building
	 *
	 * @var Building
	 *
	 * @since 1.0.0
	 */
	protected Building $building;

	/**
	 * Hot water
	 *
	 * @var string
	 *
	 * @since 1.0.0
	 */
	protected string $hotWater;

	/**
	 * Time periods
	 *
	 * @var TimePeriods
	 *
	 * @since 1.0.0
	 */
	protected TimePeriods $timePerdiods;

	/**
	 * Table names
	 */
	protected \stdClass $tableNames;

	/**
	 * Mapped form data varables
	 *
	 * @var array
	 *
	 * @since 1.0.0
	 */
	protected \stdClass $formData;

	/**
	 * Constructor
	 *
	 * @param Energiausweis
	 *
	 * @since 1.0.0
	 */
	public function __construct(Energieausweis $ec)
	{
		$this->ec = $ec;

		$this->tableNames                              = new \stdClass();
		$this->tableNames->h_erzeugung                 = 'h_erzeugung2019';
		$this->tableNames->ww_erzeugung                = 'ww_erzeugung202001';
		$this->tableNames->energietraeger              = 'energietraeger2021';
		$this->tableNames->energietraeger_umrechnungen = 'energietraeger_umrechnungen';
		$this->tableNames->klimafaktoren               = 'klimafaktoren202301';

		$this->initBuilding();
	}

	/**
	 * Get building with all data
	 *
	 * @return Building
	 *
	 * @since 1.0.0
	 */
	public function getBuilding(): Building
	{
		return $this->building;
	}

	/**
	 * Get hot water
	 *
	 * @return string 'separate', 'heater' or 'unknown'
	 *
	 * @since 1.0.0
	 */
	public function getHotWater(): string
	{
		return $this->hotWater;
	}

	/**
	 * Prepares the consumption data list for PDF
	 *
	 * @return array Consumption data list
	 *
	 * @since 1.0.0
	 */
	public function getConsumptionDataList(): array
	{
		$consumptionPeriods = $this->getConsumptionPeriods();

		$start = $consumptionPeriods[0]['start'];
		$end   = $consumptionPeriods[count($consumptionPeriods) - 1]['end'];

		$highestConsumptionHeaterKey = $this->getBuilding()->getHeaters()->getHeaterKeyByHighestEnergyValue();

		foreach ($this->getBuilding()->getHeaters() as $key => $heater) {
			$hotWater = 0;

			$data[] = array(
				'start'          => $start,
				'ende'           => $end,
				'energietraeger' => $heater->getEnergySource()->getName(),
				'primaer'        => $heater->getEnergySource()->getPrimaryEnergyFactor(),
				'gesamt'         => $heater->getHotWaterKwh() + $heater->getHeaterKwh(),
				'warmwasser'     => $heater->getHotWaterKwh(),
				'heizung'        => $heater->getHeaterKwh(),
				'klima'          => $heater->getClimateFactorAverage(),
			);
		}

		if ($this->hotWater == 'separate') {
			$data[] = array(
				'start'          => $start,
				'ende'           => $end,
				'energietraeger' => $this->getBuilding()->getHotWaterHeaters()->current()->getEnergySource()->getName(),
				'primaer'        => $this->getBuilding()->getHotWaterHeaters()->current()->getEnergySource()->getPrimaryEnergyFactor(),
				'gesamt'         => $this->getBuilding()->getHotWaterHeaters()->current()->getKWh(),
				'warmwasser'     => $this->getBuilding()->getHotWaterHeaters()->current()->getKWh(),
				'heizung'        => 0,
				'klima'          => '',
			);
		}

		if ($this->hotWater == 'unknown') {
			$data[] = array(
				'start'          => $start,
				'ende'           => $end,
				'energietraeger' => 'Warmwasserzuschlag',
				'primaer'        => $this->getBuilding()->getHeaters()->getHeaterByHighestEnergyValue()->getEnergySource()->getPrimaryEnergyFactor(),
				'gesamt'         => $this->getBuilding()->getHotWaterSurCharge(),
				'warmwasser'     => $this->getBuilding()->getHotWaterSurCharge(),
				'heizung'        => 0,
				'klima'          => '',
			);
		}

		if ($this->getBuilding()->getVacancySurcharge() > 0) {
			$hotWaterVacancySurcharge = 0;
			if ($this->getBuilding()->issetHotWaterHeaters()) {
				$hotWaterVacancySurcharge = $this->getBuilding()->getHotWaterHeaters()->getVacancySurcharge();
			}

			$data[] = array(
				'start'          => $start,
				'ende'           => $end,
				'energietraeger' => 'Leerstandszuschlag',
				'primaer'        => $this->getBuilding()->getHeaters()->getHeaterByHighestEnergyValue()->getEnergySource()->getPrimaryEnergyFactor(),
				'gesamt'         => $this->getBuilding()->getVacancySurcharge(),
				'warmwasser'     => $hotWaterVacancySurcharge,
				'heizung'        => $this->getBuilding()->getHeaters()->getVacancySurcharge(),
				'klima'          => '',
			);
		}

		if ($this->getBuilding()->issetCoolers()) {
			$data[] = array(
				'start'          => $start,
				'ende'           => $end,
				'energietraeger' => 'Kühlungszuschlag',
				'primaer'        => $this->getBuilding()->getCoolers()->current()->getEnergySource()->getPrimaryEnergyFactor(),
				'gesamt'         => $this->getBuilding()->getCoolers()->current()->getKWh(),
				'warmwasser'     => 0,
				'heizung'        => 0,
				'klima'          => '',
			);
		}

		return $data;
	}

	/**
	 * Initialize building object
	 *
	 * @since 1.0.0
	 */
	private function initBuilding(): void
	{
		$this->building = new Building($this->ec->flaeche, $this->ec->wohnungen);

		switch ($this->ec->keller) {
			case 'beheizt':
				$basement = new Basement(true);
				$this->building->setBasement($basement);
				break;
			case 'unbeheizt':
				$basement = new Basement(false);
				$this->building->setBasement($basement);
				break;
		}

		switch ($this->ec->dach) {
			case 'beheizt':
				$roof = new Roof(true);
				$this->building->setRoof($roof);
				break;
			case 'unbeheizt':
				$roof = new Roof(false);
				$this->building->setRoof($roof);
				break;
		}

		switch ($this->ec->k_info) {
			case 'vorhanden':
				$dataCoolers = array(
					0 => array(
						'energySource' => $this->getEnergySource('strom_kwh'),
						'percentage'   => 100 / $this->ec->flaeche * $this->ec->k_flaeche,
					),
				);

				$coolers = new Coolers($this->building->getUsefulArea(), $dataCoolers);
				$this->building->setCoolers($coolers);
				break;
		}

		switch ($this->ec->regenerativ_nutzung && $this->ec->regenerativ_art !== 'keine') {
			case 'warmwasser':
				$solarHeater = new SolarHeater(false, true);
				$this->building->setSolarHeater($solarHeater);
				break;
			case 'warmwasser_waermeerzeugung':
				$solarHeater = new SolarHeater(true, true);
				$this->building->setSolarHeater($solarHeater);
				break;
		}

		$hotWaterHeater = 'auto';

		switch ($this->ec->ww_info) {
			case 'ww':
				$this->hotWater = 'separate';
				break;
			case 'h':
				$this->hotWater = 'heater';
				break;
			case 'h1':
			case 'h2':
			case 'h3':
				$this->hotWater = 'heater';
				$hotWaterHeater = $this->ec->ww_info;
				break;
			case 'all':
				$this->hotWater = 'heater';
				$hotWaterHeater = 'all';
				break;
			case 'unbekannt':
				$this->hotWater = 'unknown';
				break;
		}

		$maxHeaters  = 3;
		$dataHeaters = array();

		for ($i = 0; $i < $maxHeaters; $i++) {
			if (!$this->hasHeater($i)) {
				continue;
			}

			$dataHeaters[$i] = $this->getHeater($i);
		}

		$heaters = new Heaters($this->building->getUsefulArea(), $dataHeaters, $this->hotWater == 'heater', $hotWaterHeater);
		$this->building->setHeaters($heaters);

		if ($this->hotWater == 'separate') {
			$hotWaterHeaters = new HotWaterHeaters($this->building->getUsefulArea(), array($this->getHotWaterHeater()));
			$this->building->setHotWaterHeaters($hotWaterHeaters);
		}
	}

	/**
	 * Checks if heater exist
	 *
	 * @param int Heater number (0,1 or 2)
	 *
	 * @since 1.0.0
	 */
	public function hasHeater(int $heaterNumber): bool
	{
		if ($heaterNumber === 0) {
			return true;
		}

		if ($heaterNumber === 1 && $this->ec->h2_info) {
			return true;
		}

		if ($heaterNumber === 2 && $this->ec->h2_info && $this->ec->h3_info) {
			return true;
		}

		return false;
	}

	/**
	 * Get heater
	 *
	 * @param  int Number of heater (0,1 or 2)
	 * @return Heater
	 *
	 * @since 1.0.0
	 */
	public function getHeater(int $heaterNumber): array
	{
		switch ($heaterNumber) {
			case 0:
				$heaterPrefix = 'h';
				break;
			case 1:
				$heaterPrefix = 'h2';
				break;
			case 2:
				$heaterPrefix = 'h3';
				break;
		}

		$heaterIdVarname       = $heaterPrefix . '_erzeugung'; // Beispiel: "h_erzeugung"
		$energySourceIdVarname = $heaterPrefix . '_energietraeger_' . $this->ec->$heaterIdVarname; // Beispiel:  "h2_energietraeger_standardkessel" - Kann beispielsweise Wert "fluessiggas_m3" enthalten

		$heaterId                 = $this->ec->$heaterIdVarname;
		$energySourceId           = $this->ec->$energySourceIdVarname;

		$h_custom_Varname         = $heaterPrefix . '_custom';
		$h_custom_primaer_Varname = $heaterPrefix . '_custom_primaer';

		$h_custom_co2_info_Varname = $heaterPrefix . '_custom_co2_info';
		$h_custom_co2_Varname = $heaterPrefix . '_custom_co2';

		$maxPeriods         = 3;
		$consumptionPeriods = array();
		for ($i = 0; $i < $maxPeriods; $i++) {
			$consumptionValueName = 'verbrauch' . ($i + 1) . '_' . $heaterPrefix;
			$consumption          = $this->ec->$consumptionValueName;

			$vacancyValueName = 'verbrauch' . ($i + 1) . '_leerstand';
			$vacancy          = $this->ec->$vacancyValueName;
			$climateFactor    = $this->getClimateFactor($this->ec->verbrauch_zeitraum, $i);

			$consumptionPeriods[$i] = array(
				'consumption'   => $consumption,
				'vacancy'       => $vacancy,
				'climateFactor' => $climateFactor,
			);
		}

		$energySource = $this->getEnergySource($energySourceId);

		if ($this->ec->$h_custom_Varname) {
			$energySource['primaryEnergyFactor'] = $this->ec->$h_custom_primaer_Varname;
		}

		if ($this->ec->$h_custom_co2_info_Varname) {
			$energySource['co2EmissionFactor'] = $this->ec->$h_custom_co2_Varname;
		}

		$heater = array(
			'energySource'       => $energySource,
			'heatingSystem'      => $this->getHeatingSystem($heaterId),
			'consumptionPeriods' => $consumptionPeriods,
		);

		return $heater;
	}

	/**
	 * Get heater system
	 *
	 * @param  string Heater id
	 * @return HeaterSystem
	 *
	 * @since 1.0.0
	 */
	public function getHeatingSystem(string $heaterId): array
	{
		$heaterName = wpenon_get_table_results(
			$this->tableNames->h_erzeugung,
			array(
				'bezeichnung' => array(
					'value'   => $heaterId,
					'compare' => '=',
				),
			),
			array('name'),
			true
		);

		$heaterSystem = array(
			'id'   => $heaterId,
			'name' => $heaterName,
			'type' => 'heater',
		);

		return $heaterSystem;
	}

	/**
	 * Get hot water
	 *
	 * @return HotWaterHeater
	 *
	 * @since 1.0.0
	 */
	public function getHotWaterHeater(): array
	{
		$hotWaterIdVarname = 'ww_erzeugung';
		$hotWaterId        = $this->ec->$hotWaterIdVarname;

		$energySourceIdVarname = 'ww_energietraeger_' . $hotWaterId;
		$energySourceId        = $this->ec->$energySourceIdVarname;

		$heatingSystem = $this->getHotWaterHeatingSystem($hotWaterId);
		$energySource  = $this->getEnergySource($energySourceId);

		$consumptionPeriods = array();
		for ($i = 0; $i < 3; $i++) {
			$consumptionValueName = 'verbrauch' . ($i + 1) . '_ww';
			$consumption          = $this->ec->$consumptionValueName;

			$vacancyValueName = 'verbrauch' . ($i + 1) . '_leerstand';
			$vacancy          = $this->ec->$vacancyValueName;

			$consumptionPeriods[$i] = array(
				'consumption' => $consumption,
				'vacancy'     => $vacancy,
			);
		}

		$dataHeater = array(
			'energySource'       => $energySource,
			'heatingSystem'      => $heatingSystem,
			'consumptionPeriods' => $consumptionPeriods,
		);

		return $dataHeater;
	}

	/**
	 * Get hot water system
	 *
	 * @param string System id
	 * @return HotWaterSystem
	 *
	 * @since 1.0.0
	 */
	public function getHotWaterHeatingSystem(string $heaterId): array
	{
		$heaterName = wpenon_get_table_results(
			$this->tableNames->ww_erzeugung,
			array(
				'bezeichnung' => array(
					'value'   => $heaterId,
					'compare' => '=',
				),
			),
			array('name'),
			true
		);

		$heaterSystem = array(
			'id'   => $heaterId,
			'name' => $heaterName,
			'type' => 'hotwater',
		);

		return $heaterSystem;
	}

	/**
	 * Get consumption periods
	 *
	 * @return array Consumption periods
	 *
	 * @since 1.0.0
	 */
	public function getConsumptionPeriods(): array
	{
		$timePerdiods = array();
		for ($i = 0; $i < 3; $i++) {
			$startDate        = $this->getConsumptionPeriodDate($this->ec->verbrauch_zeitraum, $i, false, 'data');
			$endDate          = $this->getConsumptionPeriodDate($this->ec->verbrauch_zeitraum, $i, true, 'data');
			$vacancyValueName = 'verbrauch' . ($i + 1) . '_leerstand';
			$vacancy          = $this->ec->$vacancyValueName;

			$timePerdiods[] = array(
				'start' => $startDate,
				'end'   => $endDate,
			);
		}

		return $timePerdiods;
	}

	/**
	 * Vacancies
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	public function getVacancies()
	{
		$vacancies = array();
		for ($i = 0; $i < 3; $i++) {
			$vacancyValueName = 'verbrauch' . ($i + 1) . '_leerstand';
			$vacancies[]      = $this->ec->$vacancyValueName;
		}

		return $vacancies;
	}

	/**
	 * Vacancy average of the years
	 *
	 * @return float
	 *
	 * @since 1.0.0
	 */
	public function getVacancyAverage(): float
	{
		$vacancySum = 0;
		foreach ($this->getVacancies() as $vacancy) {
			$vacancySum += $vacancy;
		}

		return $vacancySum / 3;
	}

	/**
	 * Get consumption period date
	 *
	 * @param  string Start date
	 * @param  int    Period number
	 * @param  bool   False if start date is needed, true if end date of period is needed
	 * @param  string Date format
	 *
	 * @return string Consumption period date
	 *
	 * @since 1.0.0
	 */
	public function getConsumptionPeriodDate(string $date, int $index = 0, bool $end = false, string $format = 'coll'): string
	{
		$year                 = $month = '';
		list($year, $month) = array_map('absint', explode('_', $date));

		$year += $index;
		$day   = 1;
		if ($end) {
			$year += 1;
			$month = ($month + 11) % 12;
			if ($month === 0) {
				$year -= 1;
				$month = 12;
			}
			$day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
		}

		$date = strtotime(zeroise($year, 4) . '-' . zeroise($month, 2) . '-' . zeroise($day, 2));

		if ($format == 'coll') {
			$format = __('M. Y', 'wpenon');
		} elseif ($format == 'data_short') {
			$format = __('m/Y', 'wpenon');
		} elseif ($format == 'data') {
			$format = __('d.m.Y', 'wpenon');
		} elseif ($format == 'slug') {
			$format = 'Y_m';
		} elseif ($format == 'timestamp') {
			return $date;
		}

		return date_i18n($format, $date);
	}

	/**
	 * Get climate factor for consumption period
	 *
	 * @param string Consumption period
	 * @param int    Index of period
	 *
	 * @return float Climate factor
	 *
	 * @since 1.2.0
	 */
	private function getClimateFactor(string $consumptionPeriod, int $index = 0): float
	{
		$year                 = $month = '';
		list($year, $month) = array_map('absint', explode('_', $consumptionPeriod));

		$year += $index;
		$day   = 1;
		$date  = strtotime(zeroise($year, 4) . '-' . zeroise($month, 2) . '-' . zeroise($day, 2));
		$date  = date_i18n('Y_m', $date);

		$climateFactors = wpenon_get_table_results(
			$this->tableNames->klimafaktoren,
			array(
				'bezeichnung' => array(
					'value'   => $this->ec->adresse_plz,
					'compare' => '>=',
				),
			),
			array(),
			true
		);

		return floatval($climateFactors->$date);
	}

	/**
	 * Get energy source
	 *
	 * @param string Energy source id
	 *
	 * @return array Energy source data
	 *
	 * @since 1.0.0
	 */
	public function getEnergySource(string $energySourceId): array
	{
		$conversions = wpenon_get_table_results(
			$this->tableNames->energietraeger_umrechnungen,
			array(
				'bezeichnung' => array(
					'value'   => $energySourceId,
					'compare' => '=',
				),
			),
			array(),
			true
		);

		$energySourceValues = wpenon_get_table_results(
			$this->tableNames->energietraeger,
			array(
				'bezeichnung' => array(
					'value'   => $conversions->energietraeger,
					'compare' => '=',
				),
			),
			array(),
			true
		);

		$energySourceArr = explode('_', $energySourceId);
		$unit            = $energySourceArr[1];

		$energySource = array(
			'id'                  => $energySourceValues->bezeichnung,
			'name'                => $energySourceValues->name,
			'kWhMultiplicator'    => $conversions->mpk,
			'primaryEnergyFactor' => $energySourceValues->primaer,
			'co2EmissionFactor'   => $energySourceValues->co2,
			'unit'                => $unit,
		);

		return $energySource;
	}
}
