<?php

class ActionDelete extends StorageApi
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
			$this->deleteFile($this->oFile);
		} else
		{
			$this->deleteFolder($this->oFolder);
		}

		$data = $this->getFolderContent($this->oFolder);
		return $response->withJson($data);
	}
}
