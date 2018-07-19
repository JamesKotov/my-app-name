<?php

class ActionGet extends StorageApi {

	public function __invoke($request, $response, $args) {

		parent::__invoke($request, $response, $args);

		if ($this->isError) {
			$data = array('status' => 'error');
			return $response->withJson($data, 404);
		}

		$data = array('status' => 'ok', 'folder' => $this->oFolder->getName(), 'subfolders' => [], 'files' => []);

		if (!is_null($this->oFile))
		{
			$data['file'] = $this->oFile->getName();

		} else
		{
			$aSubfoldersCollection = FolderQuery::create()->findByParentId($this->oFolder->getId());
			foreach ($aSubfoldersCollection as $oSubfolder) {
				$data['subfolders'][] = $oSubfolder->getName();
			}

			$aFilesCollection = FileQuery::create()->findByFolderId($this->oFolder->getId());
			foreach ($aFilesCollection as $oFile) {
				$data['files'][] = $oFile->getName();
			}
		}

		return $response->withJson($data);

	}
}
