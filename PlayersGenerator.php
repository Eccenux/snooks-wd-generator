<?php

/**
 * Simple ePub updates.
 */
class PlayersGenerator
{
	private string $basePath;
	private Logger $console;

	/** Init. */
	public function __construct(string $basePath) {
		$this->basePath = $basePath;
		$this->console = new Logger();
	}

	/**
	 * Update players.
	 */
	public function update()
	{
		// - Load & parse to objects
		$playersPath = $this->basePath."players.json";
		$countriesPath = $this->basePath."countries.json";
		// Extras
		// https://pl.wikipedia.org/wiki/Lista_kod%C3%B3w_krajowych_u%C5%BCywanych_w_sporcie
		$countriesExtraPath = $this->basePath."countries-extra.json";

		$playersJson = file_get_contents($playersPath);
		$countriesJson = file_get_contents($countriesPath);
		$countriesExtraJson = file_get_contents($countriesExtraPath);
		
		$playersData = $this->cleanup($playersJson);
		$countriesData = $this->cleanup($countriesJson);
		$countriesExtraData = $this->cleanup($countriesExtraJson);
		$countriesData = array_merge($countriesData, $countriesExtraData);

		// - Prepare players with countries data
		echo "\n\$playersData";
		var_export($playersData[0]);
		echo "\n\$countriesData";
		var_export($countriesData[0]);

		$players = $this->prepare($playersData, $countriesData);
		echo "\n\$players";
		var_export($players[0]);

		// - Merge with previous data?

		// - Save (but at least for now do not replace previous file).
		// snook.player = function (name, art, flaga)
		// new snook.player ('Adam Wicheard', '', 'ENG'),
		// new snook.player ('David Gray', 'David Gray (snookerzysta)', 'ENG'),
		// $json = "...";
		// $destPath = "...";
		// return file_put_contents($destPath, $json);
	}

	/** Cleanup and decode. */
	private function cleanup(string $wdJson): array
	{
		// Extract Q from URI
		$wdJson = str_replace('http://www.wikidata.org/entity/', '', $wdJson);
		$data = json_decode($wdJson);
		return $data;
	}


	/** Merge. */
	/**
	 * Prepare players with countries data.
	 *
	 * @param array $players
	 *	(
	 *		'item' => 'http://www.wikidata.org/entity/Q4398738',
	 *		'country' => 'http://www.wikidata.org/entity/Q21',
	 *		'LabelEN' => 'Stephen Rowlings',
	 *		'LabelDE' => 'Stephen Rowlings',
	 *		'page_titlePL' => 'Stephen Rowlings',
	 *	)
	 * @param array $countries
	 *	(
	 *		'country' => 'http://www.wikidata.org/entity/Q881',
	 *		'countryLabel' => 'Vietnam',
	 *		'countryCode' => 'VNM',
	 *	)	 
	 * @return array
	 */
	private function prepare(array $players, array $countries): array
	{
		// prepare map 
		$countryMap = array();
		foreach ($countries as $country) {
			$q = $country->country;
			$countryMap[$q] = $country;
		}
		// add country to player
		$unknown = array();
		foreach ($players as $player) {
			if (!isset($countryMap[$player->country])) {
				if (!in_array($player->country, $unknown)) {
					$unknown[] = $player->country;
				}
			} else {
				$player->country = $countryMap[$player->country];
			}
		}
		if (!empty($unknown)) {
			$this->console->warn("Note! Unknown country! ". implode(', ', $unknown));
		}
		return $players;
	}

}
