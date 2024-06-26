<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

namespace Enev\Schema202403\Calculations\Tabellen;

/**
 *  Faktor für das Baujahr von Anlagensystemen der Wohnungslüftungsanlagen Tabelle 122.
 */
class  Faktor_Baujahr_Anlagensysteme
{
	/**
	 * Zielwert für die Spalte.
	 *
	 * @var string
	 */
	protected string $lueftungssystem;

	/**
	 * Zielwert für die Zeile.
	 *
	 * @var float
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
	 * @example $a = new Hilfsenergieaufwand_Ventilatoren('abluft', 'zentral', 1989 );
	 * @param string $lueftungssystem Zielwert für die Spalte. abluft, zu_abluft oder ohne.
	 * @param string $art Zielwert für die Zeile.  zentral oder dezentral.
	 * @param int    $baujahr Zielwert für die Zeile.
	 *
	 * @return void
	 */
	public function __construct(string $lueftungssystem, string $art, int $baujahr)
	{
		$this->lueftungssystem = $lueftungssystem;
		$this->art             = $art;
		$this->baujahr         = $baujahr;
		$this->table_data      = wpenon_get_table_results('faktor_baujahr_anlagensystemen');
	}

	/**
	 * Baujahr als Slug für die Spalte.
	 *
	 * @return string
	 */
	private function baujahrslug(): string
	{
		$baujahrslug = '';
		if ($this->baujahr < 2000) {
			$baujahrslug = '1999';
		} elseif ($this->baujahr > 1999) {
			$baujahrslug = '2000';
		}
		return $baujahrslug;
	}

	private function artslug(): string
	{
		$art_slug = '';
		if ($this->art == 'zentral') {
			$art_slug = 'zentrale';
		} elseif ($this->art == 'dezentral') {
			$art_slug = 'dezentrale';
		}
		return $art_slug;
	}
	/**
	 * Umrechnungsfaktor fbaujahr.
	 *
	 * @return float
	 */
	public function fbaujahr(): float
	{ // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
		if ($this->lueftungssystem == 'ohne') {
			return 1.0;
		}

		$column_name = 'fBaujahr';
		return $this->table_data[$this->artslug() . '_' . $this->lueftungssystem . '_' . $this->baujahrslug()]->$column_name;
	}
}
