<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Enev\Schema202402\Calculations\Tabellen;

/**
 *  Faktor für das Baujahr von Anlagensystemen der Wohnungslüftungsanlagen Tabelle 121.
 */
class Faktor_Anlagensysteme_Wohnungslueftungsanlagen
{
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected string $lueftungssystem;

	/**
	 * Ist es eine zentrale oder dezentrale Anlage.
	 *
	 * @var string
	 */
	protected string $art;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var int
	 */
	protected int $baujahr;

	/**
	 * Tabellendaten aus Tabelle 122.
	 *
	 * @var array
	 */
	protected array $table_data;

	/**
	 * Konstruktor.
	 *
	 * @example $a = new Faktor_Anlagensysteme_Wohnungslueftungsanlagen('abluft', 'zentral', 1989 );
	 * @param string $lueftungssystem abluft, zu_abluft oder ohne.
	 * @param string $art Ist es eine zentrale oder dezentrale Anlage.
	 * @param int    $baujahr Zielwert für die Zeile.
	 *
	 * @return void
	 */
	public function __construct(string $lueftungssystem, bool $art, int $baujahr)
	{
		$this->lueftungssystem = $lueftungssystem;
		$this->art     = $art;
		$this->baujahr         = $baujahr;

		$this->table_data      = wpenon_get_table_results('faktor_anlagensysteme_wohnungslueftungsanlagen');
	}

	/**
	 * Baujahr als Slug für die Spalte.
	 *
	 * @return string
	 */
	private function baujahrslug(): string
	{
		$baujahrslug = '';
		if ($this->baujahr < 2010) {
			$baujahrslug = '2009';
		} elseif ($this->baujahr > 2009) {
			$baujahrslug = '2010';
		}
		return $baujahrslug;
	}

	private function art_slug(): string
	{
		if ($this->art === 'zentral') {
			return 'zentrale';
		}

		return 'dezentrale';
	}
	/**
	 * Umrechnungsfaktor fbaujahr.
	 *
	 * @return float
	 */
	public function fsystem(): float
	{ // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ($this->lueftungssystem == 'ohne') {
			return 1.0;
		}

		$column_name = 'fsystem';
		$row_name = $this->art_slug() . '_' . $this->lueftungssystem . '_' . $this->baujahrslug();
		return $this->table_data[$row_name]->$column_name;
	}
}
