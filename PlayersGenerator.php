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

		// - Save as JS
		$this->save($players);
	}

	/** Cleanup and decode. */
	private function cleanup(string $wdJson): array
	{
		// Extract Q from URI
		$wdJson = str_replace('http://www.wikidata.org/entity/', '', $wdJson);
		$data = json_decode($wdJson);
		return $data;
	}

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
			// GBR to ENG
			if ($player->country == 'Q145') {
				$player->country = 'Q21';
			}

			// gather unknown
			if (!isset($countryMap[$player->country])) {
				if (!in_array($player->country, $unknown)) {
					$unknown[] = $player->country;
				}
			} else {
				// resolve
				$player->country = $countryMap[$player->country];
			}
		}
		if (!empty($unknown)) {
			$this->console->warn("Note! Unknown country! ". implode(', ', $unknown));
		}
		return $players;
	}

	/**
	 * Save as JS.
	 *
	 * @param array $players
	 * @return void
	 */
	private function save(array $players)
	{
		// - Sort
		function cmp($a, $b) {
			$cmp = strcmp($a->country->country, $b->country->country);
			if ($cmp != 0) {
				return $cmp;
			}
			return strcmp($a->LabelEN, $b->LabelEN);
		}
		usort($players, "cmp");
		
		// - Format line
		$lines = array();
		$previousFlag = '';
		foreach ($players as $player) {
			$name = $player->LabelEN;
			$art = $player->page_titlePL;
			$flaga = $player->country->countryCode;
			if (!empty($previousFlag) && $previousFlag != $flaga) {
				$lines[] = '';
			}
			if ($art == $name) {
				// new snook.player ('Adam Wicheard', '', 'ENG'),
				$lines[] = "new snook.player (".json_encode($name).", \"\", '$flaga')";
			} else {
				// new snook.player ('David Gray', 'David Gray (snookerzysta)', 'ENG'),
				$lines[] = "new snook.player (".json_encode($name).", ".json_encode($art).", '$flaga')";
			}
			$previousFlag = $flaga;
		}

		// - Save
		$js = "snook.players = [\n\t".implode(",\n\t", $lines)."\n];";
		// replace empty lines
		$js = preg_replace('#\n[ \t]+,#', "\n", $js);
		$destPath = $this->basePath."snook.js";
		return file_put_contents($destPath, $js);
	}
}
