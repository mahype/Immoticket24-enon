<?php

namespace Enev\Schema202402\Calculations\Anlagentechnik;

use Enev\Schema202402\Calculations\Calculation_Exception;
use Enev\Schema202402\Calculations\Gebaeude\Gebaeude;
use Enev\Schema202402\Calculations\Tabellen\Luftwechsel;
use Enev\Schema202402\Calculations\Tabellen\Faktor_Anlagensysteme_Wohnungslueftungsanlagen;
use Enev\Schema202402\Calculations\Tabellen\Faktor_Baujahr_Anlagensysteme;
use Enev\Schema202402\Calculations\Tabellen\Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen;

require_once dirname(__DIR__) . '/Tabellen/Luftwechsel.php';
require_once dirname(__DIR__) . '/Tabellen/Faktor_Anlagensysteme_Wohnungslueftungsanlagen.php';
require_once dirname(__DIR__) . '/Tabellen/Faktor_Baujahr_Anlagensysteme.php';
require_once dirname(__DIR__) . '/Tabellen/Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen.php';

require_once dirname(__DIR__) . '/Helfer/Math.php';

/**
 * Berechnungen zum Luftwechsel.
 *
 * @package
 */
class Lueftung
{

	/**
	 * Gebäude.
	 *
	 * @var Gebaeude
	 */
	protected Gebaeude $gebaeude;

	/**
	 * Air system.
	 *
	 * @var string
	 */
	protected string $lueftungssystem;

	/**
	 * Art der Lüftungsanlage (zentral oder dezentral).
	 * 
	 * @var string
	 */
	protected string $art;

	/**
	 * Air system demand based.
	 *
	 * @var bool
	 */
	protected bool $bedarfsgefuehrt;

	/**
	 * Category of density.
	 *
	 * @var bool
	 */
	protected string $gebaeudedichtheit;

	/**
	 * Efficiency.
	 *
	 * @var float
	 */
	protected float $wirkungsgrad;

	/**
	 * Baujahr.
	 * 
	 * @var int
	 */
	protected int $baujahr;

	/**
	 * Luftwechsel.
	 *
	 * @var Luftwechsel
	 */
	protected Luftwechsel $luftwechsel;

	/**
	 * fbaujahr.
	 * @var mixed
	 */
	private $fbaujahr;

	/**
	 * fsystem
	 * @var mixed
	 */
	private $fsystem;

	/**
	 * Wfan0
	 * @var mixed
	 */
	private $Wfan0;

	/**
	 * Wfan
	 * @var mixed
	 */
	private $Wfan;

	/**
	 * Constructor.
	 *
	 * @param Gebaeude  $gebaeude          Gebäude.
	 * @param string    $lueftungssystem   Lüftungsyystemn (zu_abluft, abluft, ohne).
	 * @param string    $art   			   Lüftungsyystemn (zentral oder dezentral).
	 * @param bool      $bedarfsgefuehrt   Ist das Lüftungssystem bedarfsgeführt?
	 * @param string    $gebaeudedichtheit Kategorie der Gebäudedichtheit (din_4108_7,andere).
	 * @param float|int $wirkungsgrad      Der Wirklungsgrad der wärmerückgewinnung in Prozent (nur bei Zu- und Abluft)
	 * @param int       $baujahr           Baujahr der Lüftungsanlage.
	 */
	public function __construct(
		Gebaeude $gebaeude,
		string $lueftungssystem,
		string $art,
		bool $bedarfsgefuehrt = false,
		string $gebaeudedichtheit,
		float $wirkungsgrad = 0,
		int $baujahr = 0
	) {
		if ($lueftungssystem !== 'ohne' && $lueftungssystem !== 'abluft' && $lueftungssystem !== 'zu_abluft') {
			throw new \Exception('Lüftungssystem muss "ohne", "abluft" oder "zu_abluft" sein.');
		}

		if ($lueftungssystem !== 'ohne' && ($art !== 'zentral' && $art !== 'dezentral')) {
			throw new \Exception('Lüftungssystem muss "zentral" oder "dezentral" sein.');
		}

		if ($gebaeudedichtheit !== 'din_4108_7' && $gebaeudedichtheit !== 'andere') {
			throw new \Exception('Kategorie der Gebäudedichtheit muss "din_4108_7", oder "andere" sein.');
		}

		$this->gebaeude          = $gebaeude;
		$this->lueftungssystem   = $lueftungssystem;
		$this->art               = $art;
		$this->bedarfsgefuehrt   = $bedarfsgefuehrt;
		$this->gebaeudedichtheit = $gebaeudedichtheit;
		$this->wirkungsgrad      = $wirkungsgrad;
		$this->baujahr           = $baujahr;

		$this->luftwechsel = new Luftwechsel(
			$this->lueftungssystem,
			$this->gebaeudedichtheit,
			$this->bedarfsgefuehrt,
			$this->wirkungsgrad
		);

		$this->luftwechsel->gebaeude($this->gebaeude);
	}

	/**
	 * Lüftungsyystemn (zu_abluft, abluft,ohne).
	 *
	 * @return string
	 */
	public function lueftungssystem(): string
	{
		return $this->lueftungssystem;
	}

	/**
	 * Ist das Lüftungssystem bedarfsgeführt?
	 *
	 * @return bool
	 */
	public function ist_bedarfsgefuehrt(): bool
	{
		return $this->bedarfsgefuehrt;
	}

	/**
	 * Kategorie der Gebäudedichtheit (din_4108_7,andere).
	 *
	 * @return string
	 */
	public function gebaeudedichtheit(): string
	{
		return $this->gebaeudedichtheit;
	}

	/**
	 * Datenobjekt zum Luftwechsel.
	 *
	 * @return Luftwechsel
	 */
	public function luftwechsel(): Luftwechsel
	{
		return $this->luftwechsel;
	}

	/**
	 * Wirkungsgrad der Wärmerückgewinnung.
	 * 
	 * @return float
	 */
	public function wirkungsgrad(): float
	{
		return $this->wirkungsgrad;
	}

	/**
	 * Luftechselvolumen (Hv ges = 𝑛 × 𝑐 × 𝑝 × 𝑉).
	 *
	 * @return float
	 *
	 * @throws Exception
	 */
	public function hv(): float
	{
		return $this->luftwechsel->hv();
	}

	/**
	 * Maximale Heizlast.
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function h_max(): float
	{
		return $this->luftwechsel->h_max();
	}

	/**
	 * Maximale Heizlast spezifisch.
	 *
	 * @return float
	 * @throws Calculation_Exception
	 */
	public function h_max_spezifisch(): float
	{
		return $this->luftwechsel->h_max_spezifisch();
	}

	/**
	 * fsup_decr
	 *
	 * @return float
	 */
	public function fsup_decr(): float
	{
		if ($this->lueftungssystem === 'zu_abluft') {
			return 0.995;
		}
		if ($this->lueftungssystem === 'abluft') {
			return 1.0;
		}

		return 0;
	}

	public function fbetrieb(): float
	{
		//  $fbetrieb=1; //laut Tab.125, T12, Faktor für Anlagenbetrieb; BAnZ 2.3  mechanische Lüftung (ganz JAhresBetrieb ohne Bedarfsführung) und BAnz 3.3
		return 1.0;
	}

	public function fgr_exch(): float
	{
		//Laut Tab 123, T12, BanZ 11.3 
		return 1.0;
	}

	/**
	 * fbaujahr
	 *
	 * @return float
	 */
	public function fbaujahr(): float
	{
		if (!isset($this->fbaujahr)) {
			$this->fbaujahr = (new Faktor_Baujahr_Anlagensysteme($this->lueftungssystem, $this->art, $this->baujahr))->fbaujahr();
		}

		return $this->fbaujahr;
	}

	/**
	 * fsystem
	 *
	 * @return float
	 */
	public function fsystem(): float
	{
		if (!isset($this->fsystem)) {
			$this->fsystem = (new Faktor_Anlagensysteme_Wohnungslueftungsanlagen($this->lueftungssystem, $this->art, $this->baujahr))->fsystem();
		}

		return $this->fsystem;
	}

	/**
	 * Wfan0
	 *
	 * @return float
	 */
	public function Wfan0(): float
	{
		if (!isset($this->Wfan0)) {
			$this->Wfan0 = (new Hilfsenergieaufwand_Ventilatoren_Wohnungslueftungsanlagen($this->gebaeude->nutzflaeche(), $this->baujahr))->Wfan0();
		}

		return $this->Wfan0;
	}

	/**
	 * Wfan
	 *
	 * @return float
	 */
	public function Wfan(): float
	{
		if (!isset($this->Wfan)) {
			$this->Wfan = $this->Wfan0() * $this->fsystem() * $this->fbaujahr() * $this->fgr_exch() * $this->fsup_decr() * $this->fbetrieb();
		}

		return $this->Wfan;
	}

	/**
	 * Wc
	 *
	 * @return float
	 */
	public function Wc(): float
	{
		return 0.0;
	}

	/**
	 * Wpre_h
	 *
	 * @return float
	 */
	public function Wpre_h(): float
	{
		return 0.0;
	}

	/**
	 * Wrvg - Hilfsenergie für Lüftung.
	 *
	 * @return float
	 */
	public function Wrvg(): float
	{
		if ($this->lueftungssystem === 'ohne') {
			return 0.0;
		}

		return $this->Wfan() + $this->Wc() + $this->Wpre_h();
	}
}
