<?php
namespace sPHP;

class Graphic{
	#region Property variable
    private $Property = [
        //"Debug"				=>	null,
    ];
    #endregion Property variable

	#region Private variable
	//private $MaxMindGeoIP2Reader = false;
	#region Private variable

    #region Method
    public function __construct($Debug = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Resample($PictureFile, $MaximumWidth = null, $MaximumHeight = null, $SavePath = null, $Percent = null, $Width = null, $Height = null, $Type = null){
		if(is_null($Width))$Width = 0;
		if(is_null($Height))$Height = 0;
		if(is_null($MaximumWidth))$MaximumWidth = 0;
		if(is_null($MaximumHeight))$MaximumHeight = 0;
		if(is_null($Percent))$Percent = 100;
		if(is_null($Type))$Type = IMAGE_TYPE_PNG;

		$Result = false;

		if(file_exists($PictureFile)){
			$PictureFileInformation = pathinfo($PictureFile);
			if(is_null($SavePath))$SavePath = "{$PictureFileInformation["dirname"]}/";

			$PictureInformation = getimagesize($PictureFile);

			if($PictureInformation[2] == IMAGETYPE_JPEG || $PictureInformation[2] == IMAGETYPE_JPEG2000){
				$PictureHandle = imagecreatefromjpeg($PictureFile);
			}
			elseif($PictureInformation[2] == IMAGETYPE_PNG){
				$PictureHandle = imagecreatefrompng($PictureFile);
				if($PictureHandle)imagealphablending($PictureHandle, true);
			}
			elseif($PictureInformation[2] == IMAGETYPE_GIF){
				$PictureHandle = imagecreatefromgif($PictureFile);
			}

			if(!$PictureHandle){ // Error loading picture
				var_dump("Irrecoverable error with '{$PictureFile}' at " . __FILE__ . ":" . __LINE__);
			}
			else{
				if($MaximumWidth > 0 && $MaximumHeight > 0){
					if($PictureInformation[0] > $PictureInformation[1]){
						$Width = $MaximumWidth;
						$Height = $PictureInformation[1] * $MaximumWidth / $PictureInformation[0];
					}else{
						$Width = $PictureInformation[0] * $MaximumHeight / $PictureInformation[1];
						$Height = $MaximumHeight;
					}
				}
				else{
					if($Percent != 100){
						$Width = $PictureInformation[0] * $Percent / 100;
						$Height = $PictureInformation[1] * $Percent / 100;
					}
					else{
						$Width = $PictureInformation[0];
						$Height = $PictureInformation[1];
					}
				}

				$ResampleHandle = imagecreatetruecolor($Width, $Height);

				imagealphablending($ResampleHandle, false);
				imagesavealpha($ResampleHandle, true);
				imagecopyresampled($ResampleHandle, $PictureHandle, 0, 0, 0, 0, $Width, $Height, $PictureInformation[0], $PictureInformation[1]);
				imagedestroy($PictureHandle);

				$ResampleFileName = "{$SavePath}{$PictureFileInformation["filename"]}";

				if($Type == IMAGE_TYPE_JPEG || $Type == IMAGE_TYPE_JPEG2000){
					$ResampleFile = UniqueFileName("{$ResampleFileName}.jpg");
					imagejpeg($ResampleHandle, $ResampleFile, 100);
				}elseif($Type == IMAGE_TYPE_PNG){
					$ResampleFile = UniqueFileName("{$ResampleFileName}.png");
					imagepng($ResampleHandle, $ResampleFile, 0);
				}elseif($Type == IMAGE_TYPE_GIF){
					$ResampleFile = UniqueFileName("{$ResampleFileName}.gif");
					imagegif($ResampleHandle, $ResampleFile);
				}

				imagedestroy($ResampleHandle);

				$Result = basename($ResampleFile);
				//var_dump($PictureFileInformation, $PictureInformation, $PictureHandle, $Width, $Height, $ResampleFile, basename($ResampleFile)); exit;
			}
		}
		else{ // Picture file not found
			var_dump("Picture file '{$PictureFile}' not found at " . __FILE__ . ":" . __LINE__);
		}

		return $Result;
	}
    #endregion Method

    #region Property
    public function Debug($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }
    #endregion Property

	#region Function
	private function MoveUploadedItem($Path, $File, $TemporaryFile, $MustRename, $AllowedExtension, $ForbiddenExtension){
		$Result = false;

		return $Result;
	}
	#endregion Function
}
?>