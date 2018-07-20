<?php

abstract class StorageApi
{

	protected $container;
	protected $oFolder;
	protected $oFile;
	protected $isError;
	protected $filesDir;

	public function __construct($container)
	{
		$this->container = $container;
		$this->oFolder = FolderQuery::create()->findOneByParentId(-1);
		$this->oFile = null;
		$this->isError = false;

		$this->filesDir = __DIR__ . '/../../public/files';
	}

	public function parseUrl($path)
	{
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

	public function sanitizeName($name)
	{
		return str_replace("/", "_", $name);
	}

	public function getFolderContent($oFolder)
	{
		$data = array('status' => 'ok', 'folder' => $oFolder->getName(), 'subfolders' => [], 'files' => []);

		$aSubfoldersCollection = FolderQuery::create()->findByParentId($oFolder->getId());
		foreach ($aSubfoldersCollection as $oSubfolder)
		{
			$data['subfolders'][] = $oSubfolder->getName();
		}

		$aFilesCollection = FileQuery::create()->findByFolderId($oFolder->getId());
		foreach ($aFilesCollection as $oFile)
		{
			$data['files'][] = $oFile->getName();
		}

		return $data;
	}

	public function createFolder($name)
	{
		$name = $this->sanitizeName($name);

		$oFolder = FolderQuery::create()
			->filterByParentId($this->oFolder->getId())
			->filterByName($name)
			->findOne();

		if (is_null($oFolder))
		{
			$oFolder = new Folder();
			$oFolder->setParentId($this->oFolder->getId());
			$oFolder->setName($name);
			$oFolder->save();
		}
		return $oFolder;
	}

	public function saveFile($oFolder, $oUploadedFile)
	{
		if ($oUploadedFile->getError() === UPLOAD_ERR_OK)
		{
			$name = pathinfo($oUploadedFile->getClientFilename(), PATHINFO_BASENAME);

			$name = $this->sanitizeName($name);

			$oFile = FileQuery::create()
				->filterByFolderId($oFolder->getId())
				->filterByName($name)
				->findOne();

			if (!is_null($oFile))
			{
				return false;
			}

			$oFile = new File();
			$oFile->setFolderId($oFolder->getId());
			$oFile->setName($name);
			$oFile->save();

			$oUploadedFile->moveTo($this->filesDir . DIRECTORY_SEPARATOR . $oFile->getId());
			return true;
		} else
		{
			return false;
		}
	}

	public function sendFile($oFile)
	{
		$filePath = $this->filesDir . DIRECTORY_SEPARATOR . $oFile->getId();
		if (file_exists($filePath)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename="' . $oFile->getName() . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($filePath));
			readfile($filePath);
			exit;
		}
		return false;
	}

	public function __invoke($request, $response, $args)
	{
		$this->parseUrl(isset($args['path']) ? $args['path'] : '');
	}
}
