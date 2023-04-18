<?php

/**
 * Simple ePub updates.
 */
class PlayersGenerator
{
	private string $basePath;

	/** Init. */
	public function __construct(string $basePath) {
		$this->basePath = $basePath;
	}

	/**
	 * Update players.
	 */
	public function update()
	{
		// - Load & parse to objects
		$playersPath = $this->basePath."players.json";
		$countriesPath = $this->basePath."countries.json";
		$playersJson = file_get_contents($playersPath);
		$countriesJson = file_get_contents($countriesPath);
		$playersData =   json_decode($playersJson);
		$countriesData = json_decode($countriesJson);

		// - Merge
		echo "\nplayersData";
		var_export($playersData[0]);
		echo "\ncountriesData";
		var_export($countriesData[0]);

		// - Save (but at least for now do not replace previous file).
		// $json = "...";
		// $destPath = "...";
		// return file_put_contents($destPath, $json);
	}

	/** Merge. */
	private function merge(array $players, array $countries)
	{
		return array();
	}

}
