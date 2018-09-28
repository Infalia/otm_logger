<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.plugin.plugin');
require_once JPATH_COMPONENT_SITE . '/helpers/imc.php';

class plgImcotm_logger extends JPlugin
{
	public function onAfterNewIssueAdded($model, $validData, $id = null)
	{
		$details = $this->getDetails($id, $model);
		$issuePhotos = json_decode($validData['photo'], true);

		$uri = new JUri(JUri::base());
		$host = $uri->getHost();
		$protocol = $uri->getScheme();


		$step = ImcFrontendHelper::getStepByStepId($validData['stepid']);
		$catTitle = ImcFrontendHelper::getCategoryNameByCategoryId($validData['catid']);


		$moderation = false;
		$params = JComponentHelper::getParams('com_imc');
		$moderationParam = $params->get('newissueneedsmoderation');
		
		if(1 == $moderationParam) {
			$moderation = true;
		}
		
// 		echo '<pre>';
// 		print_r($validData);
// 		echo '</pre>';
// 		die;


		// OnToMap plain request
		$eventList = array('event_list' => array(
			0 => array(
				'actor' => (int) $details->sloginid,
				'timestamp' => round(microtime(true) * 1000),
				'activity_type' => 'object_created',
				'activity_objects' => array(
					0 => array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(floatval($validData['longitude']), floatval($validData['latitude']))
						),
						'properties' => array(
							'id' => (int) ($id == null ? $validData['id'] : $id),
							'hasType' => 'Issue',
							'title' => $validData['title'],
							'description' => $validData['description'],
							'category' => $catTitle,
							'address' => $validData['address'],
							'state' => $step['stepid_title'],
							'external_url' => $protocol.'://'.$host.'/issue/'.($id == null ? $validData['id'] : $id),
							'additionalProperties' => array(
								'images' => $issuePhotos['files'],
								'moderation' => $moderation
							)
						)
					)
				)
			)
		));

		if( isset($validData['belongs_to']) && !empty($validData['belongs_to']) )
		{
			// OnToMap advanced request including BELONGS_TO
			$eventList = array('event_list' => array(
				0 => array(
					'actor' => (int) $details->sloginid,
					'timestamp' => round(microtime(true) * 1000),
					'activity_type' => 'object_created',
					'activity_objects' => array(
						0 => array(
							'type' => 'Feature',
							'geometry' => array(
								'type' => 'Point',
								'coordinates' => array(floatval($validData['longitude']), floatval($validData['latitude']))
							),
							'properties' => array(
								'id' => (int) ($id == null ? $validData['id'] : $id),
								'hasType' => 'Issue',
								'title' => $validData['title'],
								'description' => $validData['description'],
								'category' => $catTitle,
								'address' => $validData['address'],
								'state' => $step['stepid_title'],
								'external_url' => $protocol.'://'.$host.'/issue/'.($id == null ? $validData['id'] : $id),
								'additionalProperties' => array(
									'images' => $issuePhotos['files'],
									'moderation' => $moderation
								)
							)
						)
					),
					'references' => array(
						0 => array(
							'application' => $this->params->get('application'),
							'external_url' => $validData['belongs_to'],
							'type' => 'BELONGS_TO'
						)
					)
				)
			));
		}



		$eventListJson = json_encode($eventList);
		$result = $this->postOTMEvent($eventListJson);
	}	

	public function onAfterCategoryModified($model, $validData, $id = null)
	{
		$details = $this->getDetails($id, $model);
		$issuePhotos = json_decode($validData['photo'], true);

		$uri = new JUri(JUri::base());
		$host = $uri->getHost();
		$protocol = $uri->getScheme();


		$step = ImcFrontendHelper::getStepByStepId($validData['stepid']);
		$catTitle = ImcFrontendHelper::getCategoryNameByCategoryId($validData['catid']);

		$moderation = false;

		if(1 == $validData['moderation']) {
			$moderation = true;
		}


		// OnToMap request
		$eventList = array('event_list' => array(
			0 => array(
				'actor' => (int) $details->sloginid,
				'timestamp' => round(microtime(true) * 1000),
				'activity_type' => 'object_updated',
				'activity_objects' => array(
					0 => array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(floatval($validData['longitude']), floatval($validData['latitude']))
						),
						'properties' => array(
							'id' => (int) ($id == null ? $validData['id'] : $id),
							'hasType' => 'Issue',
							'title' => $validData['title'],
							'description' => $validData['description'],
							'category' => $catTitle,
							'address' => $validData['address'],
							'state' => $step['stepid_title'],
							'external_url' => $protocol.'://'.$host.'/issue/'.($id == null ? $validData['id'] : $id),
							'additionalProperties' => array(
								'images' => $issuePhotos['files'],
								'moderation' => $moderation
							)
						)
					)
				)
			)
		));



		$eventListJson = json_encode($eventList);
		$result = $this->postOTMEvent($eventListJson);
	}

	public function onAfterModerationModified($model, $validData, $id = null)
	{
		$details = $this->getDetails($id, $model);
		$issuePhotos = json_decode($validData['photo'], true);

		$uri = new JUri(JUri::base());
		$host = $uri->getHost();
		$protocol = $uri->getScheme();


		$step = ImcFrontendHelper::getStepByStepId($validData['stepid']);
		$catTitle = ImcFrontendHelper::getCategoryNameByCategoryId($validData['catid']);

		$moderation = false;

		if(1 == $validData['moderation']) {
			$moderation = true;
		}


		// OnToMap request
		$eventList = array('event_list' => array(
			0 => array(
				'actor' => (int) $details->sloginid,
				'timestamp' => round(microtime(true) * 1000),
				'activity_type' => 'object_updated',
				'activity_objects' => array(
					0 => array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(floatval($validData['longitude']), floatval($validData['latitude']))
						),
						'properties' => array(
							'id' => (int) ($id == null ? $validData['id'] : $id),
							'hasType' => 'Issue',
							'title' => $validData['title'],
							'description' => $validData['description'],
							'category' => $catTitle,
							'address' => $validData['address'],
							'state' => $step['stepid_title'],
							'external_url' => $protocol.'://'.$host.'/issue/'.($id == null ? $validData['id'] : $id),
							'additionalProperties' => array(
								'images' => $issuePhotos['files'],
								'moderation' => $moderation
							)
						)
					)
				)
			)
		));



		$eventListJson = json_encode($eventList);
		$result = $this->postOTMEvent($eventListJson);
	}

	public function onAfterStepModified($model, $validData, $id = null)
	{
		$details = $this->getDetails($id, $model);
		$issuePhotos = json_decode($validData['photo'], true);

		$uri = new JUri(JUri::base());
		$host = $uri->getHost();
		$protocol = $uri->getScheme();


		$step = ImcFrontendHelper::getStepByStepId($validData['stepid']);
		$catTitle = ImcFrontendHelper::getCategoryNameByCategoryId($validData['catid']);

		$moderation = false;

		if(1 == $validData['moderation']) {
			$moderation = true;
		}


		// OnToMap request
		$eventList = array('event_list' => array(
			0 => array(
				'actor' => (int) $details->sloginid,
				'timestamp' => round(microtime(true) * 1000),
				'activity_type' => 'issue_status_updated',
				'activity_objects' => array(
					0 => array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(floatval($validData['longitude']), floatval($validData['latitude']))
						),
						'properties' => array(
							'id' => (int) ($id == null ? $validData['id'] : $id),
							'hasType' => 'Issue',
							'title' => $validData['title'],
							'description' => $validData['description'],
							'category' => $catTitle,
							'address' => $validData['address'],
							'state' => $step['stepid_title'],
							'external_url' => $protocol.'://'.$host.'/issue/'.($id == null ? $validData['id'] : $id),
							'additionalProperties' => array(
								'images' => $issuePhotos['files'],
								'moderation' => $moderation
							)
						)
					)
				)
			)
		));



		$eventListJson = json_encode($eventList);
		$result = $this->postOTMEvent($eventListJson);
	}

	public function onAfterNewCommentAdded($model, $validData, $id = null)
	{
		$details = $this->getDetails($id, $model);

		$uri = new JUri(JUri::base());
		$host = $uri->getHost();
		$protocol = $uri->getScheme();


		// OnToMap request
		$eventList = array('event_list' => array(
			0 => array(
				'actor' => (int) $details->sloginid,
				'timestamp' => round(microtime(true) * 1000),
				'activity_type' => 'object_created',
				'activity_objects' => array(
					0 => array(
						'type' => 'Feature',
						'geometry' => array(
							'type' => 'Point',
							'coordinates' => array(floatval($validData['longitude']), floatval($validData['latitude']))
						),
						'properties' => array(
							'id' => (int) $validData['comment_id'],
							'hasType' => 'Comment',
							'body' => $validData['comment_body'],
							'external_url' => $protocol.'://'.$host.'/comment/'.$validData['comment_id']
							//'additionalProperties' => array()
						)
					)
				),
				'references' => array(
					0 => array(
						'application' => $protocol.'://'.$host,
						'external_url' => $protocol.'://'.$host.'/issue/'.($id == null ? $validData['id'] : $id)
					)
				)
			)
		));


		if(0 < $validData['comment_parent_id']) {
			$eventList['event_list'][0]['references'][1]['application'] = $protocol.'://'.$host;
			$eventList['event_list'][0]['references'][1]['external_url'] = $protocol.'://'.$host.'/comment/'.$validData['comment_parent_id'];
		}

		$eventListJson = json_encode($eventList);
		$result = $this->postOTMEvent($eventListJson);

	}

	private function getDetails($id, $model) 
	{
		$issueid = $id;

		//check if issue added from frontend
		if($id == null){
			$issueid = $model->getItem()->get('id');
		} 


		require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/issue.php';
		//JModelLegacy::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR . '/models');
		$issueModel = JModelLegacy::getInstance( 'Issue', 'ImcModel' );
		$item = $issueModel->getItem($issueid);

		$userid = $item->get('created_by');
		$username = JFactory::getUser($userid)->name;
		$usermail = JFactory::getUser($userid)->email;

		//check if social plugin is enabled and user is on social table
		if(JPluginHelper::isEnabled('slogin_integration', 'profile'))
		{
			$socialUser = ImcFrontendHelper::getSocialUserByUserId($userid);

			if(!empty($socialUser)) {
				$socialId = $socialUser['slogin_id'];
				$socialEmail = $socialUser['email'];

				if(!empty($socialEmail)) {
					if(JFactory::getMailer()->ValidateAddress($socialEmail))
					{
						$usermail = $socialEmail;
					}
				}
			}

			//$socialEmail = ImcFrontendHelper::getSocialEmail($userid);
		}

		$details = new stdClass();
		$details->issueid = $issueid;
		$details->emails = $emails;
		$details->userid = $userid;
		$details->sloginid = $socialId;
		$details->username = $username;
		$details->usermail = $usermail;

		return $details;
	}

	private function getOTMEvents($params = array())
	{
		$queryString = '';
		
		if(!empty($params)) {
			$queryString = '?'.http_build_query($params);
		}


		$curl = curl_init($this->params->get('eventsurl').$queryString);
		
		$fp = fopen(dirname(__FILE__).'/otm_error_log.txt', 'w');
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_STDERR, $fp);

		// curl_setopt($curl, CURLOPT_POST, 1);
		// curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM'); 
		curl_setopt($curl, CURLOPT_SSLCERT, $this->params->get('certpath'));
		$output = curl_exec( $curl );
		curl_close($curl);

		$result = json_decode($output);

		if(empty($result)){
			echo 'OTM error';
			exit;
		}
		if(!empty($result->error)){
			echo 'OTM error - '. $result->message;
			exit;
		}


		return $result;
	}

	private function getOTMMappings()
	{
		$curl = curl_init($this->params->get('mappingsurl'));
		
		$fp = fopen(dirname(__FILE__).'/otm_error_log.txt', 'w');
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_STDERR, $fp);

		// curl_setopt($curl, CURLOPT_POST, 1);
		// curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM'); 
		curl_setopt($curl, CURLOPT_SSLCERT, $this->params->get('certpath'));
		$output = curl_exec( $curl );
		curl_close($curl);

		$result = json_decode($output);


		if(empty($result)){
			echo 'OTM error';
			exit;
		}
		if(!empty($result->error)){
			echo 'OTM error - '. $result->message;
			exit;
		}


		return $result;
	}

	private function postOTMEvent($paramsJsonStr = '')
	{
		$curl = curl_init($this->params->get('eventsurl'));
		
		$fp = fopen(dirname(__FILE__).'/otm_error_log.txt', 'w');
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_STDERR, $fp);

		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsJsonStr);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM'); 
		curl_setopt($curl, CURLOPT_SSLCERT, $this->params->get('certpath'));
		$output = curl_exec( $curl );
		curl_close($curl);

		$result = json_decode($output);

		if(empty($result)){
			echo 'OTM error';
			exit;
		}
		if(!empty($result->error)){
			echo 'OTM error - '. $result->message;
			exit;
		}


		return $result;
	}

	private function postOTMMappings($paramsJsonStr = '')
	{
		$curl = curl_init($this->params->get('mappingsurl'));
		
		$fp = fopen(dirname(__FILE__).'/otm_error_log.txt', 'w');
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_STDERR, $fp);

		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $paramsJsonStr);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($curl, CURLOPT_SSLCERTTYPE, 'PEM'); 
		curl_setopt($curl, CURLOPT_SSLCERT, $this->params->get('certpath'));
		$output = curl_exec( $curl );
		curl_close($curl);

		$result = json_decode($output);

		if(empty($result)){
			echo 'OTM error';
			exit;
		}
		if(!empty($result->error)){
			echo 'OTM error - '. $result->message;
			exit;
		}


		return $result;
	}
}
