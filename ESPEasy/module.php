<?php

class ESPEasy extends IPSModule {

    public function Create() {
        parent::Create();
        $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');

        $this->RegisterPropertyString('sysname','');
		$this->RegisterPropertyString('devlist','');
    }

    public function ApplyChanges() {
        parent::ApplyChanges();

        $this->ConnectParent('{C6D2AEB3-6E1F-4B2E-8E69-3A1A00246850}');		

        if (!empty($this->ReadPropertyString('sysname'))){
			$sysname=$this->ReadPropertyString('sysname');
			$this->SetReceiveDataFilter('.*' . $sysname . '.*'); 
					
			if(!empty($this->ReadPropertyString("devlist"))){
				$arrString = $this->ReadPropertyString("devlist");
				$dev_arr = json_decode($arrString);			
			
				foreach($dev_arr as $key=>$dev){						
				
					if (IPS_VariableExists(@$this->GetIDForIdent('ESPEasy_'.$dev->tskname.'_'.$dev->valname))){
						IPS_SetName ($this->GetIDForIdent('ESPEasy_'.$dev->tskname.'_'.$dev->valname), $dev->varname);
					}
					
					switch($dev->type){						
							case -1:
								$this->RegisterVariableBoolean('ESPEasy_'.$dev->tskname.'_'.$dev->valname, $dev->varname, $dev->varprofile);
								$this->EnableAction('ESPEasy_'.$dev->tskname.'_'.$dev->valname);
								break;							
							case 0:
								$this->RegisterVariableBoolean('ESPEasy_'.$dev->tskname.'_'.$dev->valname, $dev->varname, $dev->varprofile);
								break;
							case 1:
								$this->RegisterVariableInteger('ESPEasy_'.$dev->tskname.'_'.$dev->valname, $dev->varname, $dev->varprofile);								
								break;							
							case 2:
								$this->RegisterVariableFloat('ESPEasy_'.$dev->tskname.'_'.$dev->valname, $dev->varname, $dev->varprofile);
								break;							
							case 3:
								$this->RegisterVariableString('ESPEasy_'.$dev->tskname.'_'.$dev->valname, $dev->varname, $dev->varprofile);
								break;	
					}
				}
			}	
		}	

		if(!IPS_VariableProfileExists('ESPEasy.Reachable')){
			IPS_CreateVariableProfile ('ESPEasy.Reachable',0);
			IPS_SetVariableProfileAssociation('ESPEasy.Reachable', false, 'Offline', '', 0xFF0000);
			IPS_SetVariableProfileAssociation('ESPEasy.Reachable', true, 'Online', '', 0x00FF00);
			IPS_SetVariableProfileIcon('ESPEasy.Reachable',  'Network');
		}
        $this->RegisterVariableBoolean('ESPEasy_Reachable', 'Reachable', 'ESPEasy.Reachable');
    }

    public function ReceiveData($JSONString) {
        $this->SendDebug(__FUNCTION__, $JSONString, 0);
        $data = json_decode($JSONString);
        $this->SendDebug(__FUNCTION__ . ' Topic', $data->Topic, 0);
        $this->SendDebug(__FUNCTION__ . ' Payload', $data->Payload, 0);		
		
		if (!empty($this->ReadPropertyString('sysname'))){
			$sysname=$this->ReadPropertyString('sysname');
			
			if($data->Topic==$sysname."/status/LWT"){
				
				if($data->Payload=="Connected"){
					SetValue($this->GetIDForIdent('ESPEasy_Reachable'), true);
				}else{
					SetValue($this->GetIDForIdent('ESPEasy_Reachable'), false);
				}
			}
			
			if (!empty($this->ReadPropertyString('devlist'))){
				$arrString = $this->ReadPropertyString("devlist");
				$dev_arr = json_decode($arrString);
				
				foreach($dev_arr as $key=>$dev){
					$topic=$sysname."/".$dev->tskname."/".$dev->valname;
					
					if($data->Topic==$topic){
						
						if($dev->type<=0 && $dev->inverted){
							$data->Payload=!$data->Payload;
						}						
						setvalue($this->GetIDForIdent('ESPEasy_'.$dev->tskname.'_'.$dev->valname),$data->Payload);					
					}				
				}			
			}
		}
    }

    public function RequestAction($Ident, $value) {
        $this->SendDebug(__FUNCTION__ . ' Value', $value, 0);
		
		if(!empty($this->ReadPropertyString('sysname'))){
			$topic = $this->ReadPropertyString('sysname') . '/cmd';
			
			if (!empty($this->ReadPropertyString('devlist'))){
				$arrString = $this->ReadPropertyString("devlist");
				$dev_arr = json_decode($arrString);				
				foreach($dev_arr as $key=>$dev){
					
					if($Ident=='ESPEasy_'.$dev->tskname.'_'.$dev->valname){					
						$gpio=$dev->gpio;						
					}
					
					if($dev->type<=0 && $dev->inverted){
						$value=!$value;
					}	
								
				}			
			}
		}			

        $this->sendMQTT($topic, 'GPIO,' . $gpio . ',' . intval($value));
    }

    protected function sendMQTT($Topic, $Payload) {
        $Data['DataID'] = '{043EA491-0325-4ADD-8FC2-A30C8EEB4D3F}';
        $Data['PacketType'] = 3;
        $Data['QualityOfService'] = 0;
        $Data['Retain'] = false;
        $Data['Topic'] = $Topic;
        $Data['Payload'] = $Payload;

        $DataJSON = json_encode($Data, JSON_UNESCAPED_SLASHES);
        $this->SendDebug(__FUNCTION__ . ' Topic', $Data['Topic'], 0);
        $this->SendDebug(__FUNCTION__ . ' Payload', $Data['Payload'], 0);
        $this->SendDebug(__FUNCTION__, $DataJSON, 0);
        $this->SendDataToParent($DataJSON);
    }
}