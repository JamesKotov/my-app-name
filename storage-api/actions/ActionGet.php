<?php

class ActionGet extends StorageApi
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
			if (!$this->sendFile($this->oFile))
			{
				$data = array('status' => 'error', 'code' => 'can\'t send file');
				return $response->withJson($data, 500);
			}
		} else
		{
			$data = $this->getFolderContent($this->oFolder);
			return $response->withJson($data);
		}
	}
}
