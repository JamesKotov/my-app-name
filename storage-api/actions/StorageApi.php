<?php

abstract class StorageApi {

	protected $container;
	protected $oFolder;
	protected $oFile;
	protected $isError;

	public function __construct($container) {
		$this->container = $container;
		$this->oFolder = FolderQuery::create()->findOneByParentId(-1);
		$this->oFile = null;
		$this->isError = false;
	}

	public function parseUrl($path) {
		$aPath = array_filter(explode('/', trim($path, '/')));

		$oFile = null;

		$iParentId = $this->oFolder->getId();

		if (sizeof($aPath))
		{
			foreach ($aPath as $index => $sPathItem)
			{
				$this->oFolder = FolderQuery::create()
					->filterByParentId($iParentId)
					->filterByName($sPathItem)
					->findOne();

				// Found
				if (!is_null($this->oFolder))
				{
					$iParentId = $this->oFolder->getId();
				} else
				{
					// Stop search if folder not found
					$index--;
					$this->oFolder = FolderQuery::create()->findPK($iParentId);
					break;
				}
			}
			$aPath = array_slice($aPath, $index + 1);

			if (sizeof($aPath) > 1)
			{
				$this->isError = true;
			}

			if (sizeof($aPath) === 1)
			{
				// try to find a file
				$this->oFile = FileQuery::create()
					->filterByFolderId($iParentId)
					->filterByName($aPath[0])
					->findOne();

				if (is_null($this->oFile))
				{
					$this->isError = true;
				}
			}
		}
	}

	public function __invoke($request, $response, $args) {
		$this->parseUrl(isset($args['path']) ? $args['path'] : '');
	}
}
