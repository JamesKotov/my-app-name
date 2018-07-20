<?php

class ActionPost extends StorageApi
{

	public function __invoke($request, $response, $args)
	{

		parent::__invoke($request, $response, $args);

		if ($this->isError)
		{
			$data = array('status' => 'error', 'code' => 'path not found');
			return $response->withJson($data, 404);
		}

		if (!is_null($this->oFile))
		{
			$data = array('status' => 'error', 'code' => 'can\'t post to file');
			return $response->withJson($data, 500);
		}

		$oTargetFolder = $this->oFolder;

		$body = $request->getParsedBody();
		if ($body['folder'])
		{
			$oTargetFolder = $this->createFolder($body['folder']);
		}

		$files = $request->getUploadedFiles();

		if ($files['file'])
		{
			if (!$this->saveFile($oTargetFolder, $files['file']))
			{
				$data = array('status' => 'error', 'code' => 'can\'t save file');
				return $response->withJson($data, 500);
			}
		}

		$data = $this->getFolderContent($oTargetFolder);
		return $response->withJson($data);

	}
}
