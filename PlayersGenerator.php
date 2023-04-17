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
		$playersPath = $this->basePath."";
		$countriesPath = $this->basePath."";
		$playersJson = file_get_contents($playersPath);
		$countriesJson = file_get_contents($countriesPath);

		// - Merge 

		// - Save (but at least for now do not replace previous file).
		$json = "...";
		$destPath = "...";
		return file_put_contents($destPath, $json);
	}

	/** Merge. */
	private function merge(array $players, array $countries)
	{
		return array();
	}

}
