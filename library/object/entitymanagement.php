<?php
/*
    Name:           EntityManagement
    Purpose:        Entity management object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 15, 2018 11:17 PM
*/

namespace sPHP;

class EntityManagement{
    private $Property = [
        "Table"						=>	null,
		"Environment"				=>	null,
		"Verbose"					=>	null,
		"ListColumn"				=>	[],
		"SearchSQL"					=>	null,
		"SearchUIHTML"				=>	null,
		"Action"					=>	[],
		"BatchActionHTML"			=>	null,
		"OrderBy"					=>	null,
		"Order"						=>	"ASC",
		"RecordsPerPage"			=>	50,
		"URL"						=>	null,
		"BaseURL"					=>	null,
		"IconURL"					=>	null,
		"SearchInputPrefix"			=>	null,

		"EncryptionKey"				=>	null,
		"FieldCaptionWidth"			=>	null,
		"FieldCaptionInlineWidth"	=>	null,
		"FieldContentFullWidth"		=>	null,
		"InputWidth"				=>	null,
		"InputInlineWidth"			=>	null,
		"InputFullWidth"			=>	null,
		"InputDateWidth"			=>	null,
		"InputTimeWidth"			=>	null,
		"TempPath"					=>	"./temp/",

		"IntermediateEntity"		=>	null,
		"DefaultFromSearchColumn"	=>	null,
		"InputUIHTML"				=>	null,
		"InputValidation"			=>	[],
		"ValidateInput"				=>	null,
		"UploadPath"				=>	null,
		"ThumbnailColumn"			=>	[],
		"ThumbnailMaximumDimension"	=>	48,
		"BeforeInput"				=>	null,
		"AfterInput"				=>	null,

		"WHEREClauseOnDelete"		=>	null,

		"ImportField"				=>	[],
        "DatagridCSSSelector"		=>	null,
		"ListTitle"					=>	null,
		"InputTitle"				=>	null,
		"ListRowExpandURL"			=>	null,
		"ListRowCSSSelector"		=>	null,

        "ListHTML"					=>	null,
        "InputHTML"					=>	null,
        "ImportHTML"				=>	null,
        "ExportHTML"				=>	null,

		"LowercaseEntityName"		=>	null,
        "EntityID"					=>	null,
        "VisibleRecordset"          =>  [], // Recordset that are loaded in the datagrid, limited with FROM & TO
        "SearchURLArgument"         =>  null, // Generate URL argument for search values
    ];

    #region Variable
    //private $Buffer = [];
    #endregion Variable

    #region Method
    public function __construct($Table = null, $Environment = null, $Verbose = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){
		$Result = true;

        return $Result;
    }

	public function LoadExistingData(){
		if($this->Property["EntityID"]){ // Existing record ID
            $PrimaryKey = $this->Property["Table"]->Structure()["Primary"][0];
            $Record = $this->Property["Table"]->Get("{$this->Property["Table"]->Alias()}.{$PrimaryKey} = {$this->Property["EntityID"]}");

            if(isset($Record[0])){ // Existing record found
                foreach($Record[0] as $Key => $Value)SetVariable($Key, $Value); // Load existing data into the input form
                
                foreach(ListToArray($this->Property["IntermediateEntity"]) as $OptionEntity){ // Load option data from intermediate table
                    if(in_array("{$this->Property["Table"]->Prefix()}{$this->Property["LowercaseEntityName"]}" . strtolower($OptionEntity) . "", $this->Property["Table"]->Database()->Table())){
                        $IntermediateTable = new Database\Table(
                            "{$this->Property["Table"]->EntityName()}{$OptionEntity}",
                            "_IE",
                            null,
                            null,
                            $this->Property["Table"]->SQLSELECTPath(),
                            $this->Property["Table"]->Database(),
                            $this->Property["Table"]->Prefix()
                        );

                        $OptionRecordset = $IntermediateTable->Get("{$IntermediateTable->Alias()}.{$this->Property["Table"]->Structure()["Primary"][0]} = {$this->Property["EntityID"]} AND {$this->Property["Table"]->EntityName()}{$OptionEntity}IsActive = 1", null, null, null, null, null, null);
                        if(is_array($OptionRecordset))foreach($OptionRecordset as $Option)$_POST["{$OptionEntity}ID_{$Option["{$OptionEntity}ID"]}"] = $Option["{$OptionEntity}ID"];
                    }
                }

                $Result = true;
            }
            else{ // Existing record not found
                $Result = false;
            }
		}
		else{ // No existing record ID specified
			$Result = false;
		}

		foreach(ListToArray($this->Property["DefaultFromSearchColumn"]) as $Column)SetVariable($Column, SetVariable("{$this->Property["SearchInputPrefix"]}{$Column}"));

		return $Result;
	}

	public function Input($WHERE = null){
        /*
            $WHERE = Additional WHERE clause upon UPDATE
        */

		$Form = new HTML\UI\Form(null, null, null, $this->Property["EncryptionKey"], null, null, null, null, $_POST["_ID"], null, null, null, $this->Property["InputValidation"]);

		if($Form->Verify()){
			if(is_null($this->Property["ValidateInput"]) || ($ValidateInput = $this->Property["ValidateInput"]($this->Property["Table"]->EntityName(), $this->Property["Table"]->Database(), $this->Property["Table"], $this->Property["Table"]->Structure()["Primary"][0], $this->Property["EntityID"])) === true){
				Upload($EntityUploadPath = "{$this->Property["UploadPath"]}{$this->Property["LowercaseEntityName"]}/");
				$FilePOSTKey = array_keys($_FILES); // Key list of file POST variables

				// Load existing record
				if($this->Property["EntityID"])$Record = $this->Property["Table"]->Get("{$this->Property["Table"]->Alias()}.{$this->Property["Table"]->Structure()["Primary"][0]} = {$this->Property["EntityID"]}")[0];
                //var_dump($Record); exit;
				// Delete existing files for uploaded new file or when asked to delete
				foreach($FilePOSTKey as $Column){
					// Set POST value for no file uploaded
					if(!isset($_POST["{$Column}"]) || $_POST["{$Column}"] === false)$_POST["{$Column}"] = null;

					if(isset($Record) && $Record["{$Column}"]){ // Existing record and file data exists
						if(
								// New file uploaded or asked to delete existing file
								($_POST["{$Column}"] || isset($_POST["__DeleteExistingFile_{$Column}"]))
							&&	file_exists($ExistingFile = "{$EntityUploadPath}{$Record["{$Column}"]}") // File exists
						)unlink($ExistingFile); // Delete existing file

						if( // No file uploaded and didn't ask to delete existing file either
							!$_POST["{$Column}"] && !isset($_POST["__DeleteExistingFile_{$Column}"])
						)$_POST["{$Column}"] = $Record["{$Column}"]; // Set existing value to the POST variable
					}
				}

				// Create thumbnail
				foreach(array_intersect(ListToArray($this->Property["ThumbnailColumn"]), $FilePOSTKey, $this->Property["Table"]->Structure()["String"]) as $Column){
					$_POST[$ThumbnailField = "{$Column}Thumbnail"] = $_POST[$Column] ? (!isset($Record) || $_POST[$Column] != $Record[$Column] ? Graphic\Resample("{$EntityUploadPath}{$_POST[$Column]}", $this->Property["ThumbnailMaximumDimension"], $this->Property["ThumbnailMaximumDimension"]) : $Record[$ThumbnailField]) : null;
					if(isset($Record) && $_POST[$ThumbnailField] != $Record[$ThumbnailField] && $Record[$ThumbnailField] && file_exists($ExistingFile = "{$EntityUploadPath}{$Record[$ThumbnailField]}"))unlink($ExistingFile);
				}

				if(!is_null($this->Property["BeforeInput"]))$this->Property["BeforeInput"]($this->Property["Table"]->EntityName(), isset($Record) ? $Record : null);
                //var_dump($_POST);
                $this->Property["Table"]->Put(
                    $_POST, 
                    $this->Property["EntityID"] ? "{$this->Property["Table"]->Structure()["Primary"][0]} = {$this->Property["EntityID"]}" . ($WHERE ? " AND ({$WHERE})" : null) . "" : null, 
                    null, 
                    $this->Property["Verbose"]
                );
                
				$AffectedRecord = $this->Property["Table"]->Get("{$this->Property["Table"]->Alias()}.{$this->Property["Table"]->Structure()["Primary"][0]} = " . ($this->Property["EntityID"] ? $this->Property["EntityID"] : "@@IDENTITY") . "", null, null, null, null, null, $this->Property["Verbose"])[0];

				if(!is_null($this->Property["AfterInput"]))$this->Property["AfterInput"]($this->Property["Table"]->EntityName(), $AffectedRecord, isset($Record) ? $Record : null, $this->Property["Table"]);

				// Insert option data through intermediate table
				foreach(ListToArray($this->Property["IntermediateEntity"]) as $OptionEntity){
					if(in_array("{$this->Property["Table"]->Prefix()}{$this->Property["LowercaseEntityName"]}" . strtolower($OptionEntity) . "", $this->Property["Table"]->Database()->Table())){ //DebugDump($OptionEntity);
						$IntermediateTable = new Database\Table(
							"{$this->Property["Table"]->EntityName()}{$OptionEntity}",
							"_IE",
							null,
							null,
							$this->Property["Table"]->SQLSELECTPath(),
							$this->Property["Table"]->Database(),
							$this->Property["Table"]->Prefix()
						); //DebugDump($IntermediateTable);

						$IntermediateTable->Remove("{$this->Property["Table"]->Structure()["Primary"][0]} = {$AffectedRecord[$this->Property["Table"]->Structure()["Primary"][0]]}");

						foreach($_POST as $Key => $OptionID){ //DebugDump([$Key, $OptionID]);
							if(substr($Key, 0, strlen("{$OptionEntity}ID_")) == "{$OptionEntity}ID_"){ //DebugDump([$Key, $OptionID]);
								$IntermediateOptionData[] = [
									"{$this->Property["Table"]->Structure()["Primary"][0]}"=>$AffectedRecord[$this->Property["Table"]->Structure()["Primary"][0]],
									"{$OptionEntity}ID"=>$OptionID,
									"{$this->Property["Table"]->EntityName()}{$OptionEntity}IsActive"=>1
								];
							}
						}

						if(isset($IntermediateOptionData))$IntermediateTable->Put($IntermediateOptionData);
					}
				}

				\sPHP::$Log->Put("" . \sPHP::$User->Name() . " (" . \sPHP::$User->UserGroupIdentifierHighest() . ")", ["ID" => $AffectedRecord[$this->Property["Table"]->Structure()["Primary"][0]], "WHERE" => $WHERE, ], null, LOG_TYPE_APPLICATION, $this->Property["EntityID"] ? "Edit" : "Add new", "{$this->Property["Table"]->FormalName()}", "Database");
                print "" . HTML\UI\MessageBox("Information saved into the database successfully.", "System") . "";

				//$Result = true;
				$Result = $AffectedRecord;
			}
			else{
				$Form->ErrorMessage($ValidateInput);

				$Result = false;
			}
		}
		else{
			$Result = false;
		}

		return $Result;
	}

	public function Delete($Where = null){
		$WhereClause[] = "{$this->Property["Table"]->Structure()["Primary"][0]} IN (" . (is_array($this->Property["EntityID"]) ? implode(", ", $this->Property["EntityID"]) : $this->Property["EntityID"]) . ")";
		if($Where)$WhereClause[] = "({$Where})";

		$this->Property["Table"]->Remove(
			implode(" AND ", $WhereClause),
			null,
			null
		);

        \sPHP::$Log->Put("" . \sPHP::$User->Name() . " (" . \sPHP::$User->UserGroupIdentifierHighest() . ")", ["WhereClause" => $WhereClause, ], null, LOG_TYPE_APPLICATION, "Remove", "{$this->Property["Table"]->FormalName()}", "Database");
		print "" . HTML\UI\MessageBox("Information removed from system.", "System") . "";

		return true;
	}

	public function Import($AfterParse = null, $Resource = []){
        $Result = true;

		Upload("{$this->Property["TempPath"]}");
		$DataFile = "{$this->Property["TempPath"]}{$_POST["{$this->Property["Table"]->EntityName()}DataFile"]}";
        $this->Property["Table"]->Import($DataFile, $this->Property["ImportField"], null, null, null, $AfterParse, $Resource);
        unlink($DataFile);

        return $Result;
	}

	public function Export(){
        //var_dump($this->Property["Environment"]); exit;
		foreach($_POST as $ColumnKey=>$ThisColumn)if(substr($ColumnKey, 0, strlen($Marker = "Column_")) == $Marker)$Column[] = substr($ColumnKey, strlen("Column_"));
		//return $this->Property["Table"]->Export($Column, null, $_POST["Format"], "{$this->Property["TempPath"]}Export_{$this->Property["Table"]->EntityName()}.csv", $_POST["{$this->Property["Table"]->EntityName()}IDList"] ? "OP.{$this->Property["Table"]->EntityName()}ID IN ({$_POST["{$this->Property["Table"]->EntityName()}IDList"]})" : null, "{$_POST["OrderBy"]} {$_POST["Order"]}");

		return $this->Property["Table"]->Export(
            $Column,
            null,
            $_POST["Format"],
            null,
            $_POST["{$this->Property["Table"]->EntityName()}IDList"] ? "OP.{$this->Property["Table"]->EntityName()}ID IN ({$_POST["{$this->Property["Table"]->EntityName()}IDList"]})" : $_POST["SearchSQL"],
            "{$_POST["OrderBy"]} {$_POST["Order"]}"
        );
	}
    #endregion Method

    #region Property
    public function Table($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["LowercaseEntityName"] = strtolower($this->Property[__FUNCTION__]->EntityName());
			$this->Property["EntityID"] = isset($_POST["{$this->Property["Table"]->EntityName()}ID"]) && (($_POST["{$this->Property["Table"]->EntityName()}ID"] = is_array($_POST["{$this->Property["Table"]->EntityName()}ID"]) ? $_POST["{$this->Property["Table"]->EntityName()}ID"] : intval($_POST["{$this->Property["Table"]->EntityName()}ID"])) || is_array($_POST["{$this->Property["Table"]->EntityName()}ID"])) ? $_POST["{$this->Property["Table"]->EntityName()}ID"] : 0;

			$this->Property["ListHTML"] = null;
			$this->Property["InputHTML"] = null;
			$this->Property["DeleteHTML"] = null;
			$this->Property["ImportHTML"] = null;
			$this->Property["ExportHTML"] = null;

			$Result = true;
        }

        return $Result;
    }

    public function Environment($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Verbose($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ListColumn($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SearchSQL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SearchUIHTML($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Action($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BatchActionHTML($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function OrderBy($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Order($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function RecordsPerPage($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function URL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BaseURL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function IconURL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function SearchInputPrefix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;
            $this->Property["SearchURLArgument"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function EncryptionKey($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FieldCaptionWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FieldCaptionInlineWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function FieldContentFullWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function InputWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function InputInlineWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function InputFullWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function InputDateWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function InputTimeWidth($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function TempPath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function IntermediateEntity($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }
    
    public function DefaultFromSearchColumn($Value = null){ // Keep the search values in the new input form
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function InputUIHTML($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["InputHTML"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function InputValidation($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ValidateInput($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function UploadPath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ThumbnailColumn($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ThumbnailMaximumDimension($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BeforeInput($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AfterInput($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function WHEREClauseOnDelete($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ImportField($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function DatagridCSSSelector($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ListTitle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(!$this->Property["InputTitle"])$this->Property["InputTitle"] = $this->Property[__FUNCTION__];

            $Result = true;
        }

        return $Result;
    }

    public function InputTitle($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ListRowExpandURL($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ListRowCSSSelector($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function ListHTML(){
        if(is_null($this->Property[__FUNCTION__])){
            foreach($_POST as $Key => $Value){ // Generate URL arguments for search field(s)
				if(
						!is_array($Value)
                    &&  substr($Key, 0, strlen($this->Property["SearchInputPrefix"])) == $this->Property["SearchInputPrefix"]
					&&	strlen($Value)
				)$SearchArgument[] = "{$Key}=" . urlencode($Value) . "";
			} //var_dump($_POST, $this->Property["OrderBy"], SetVariable("OrderBy", $this->Property["OrderBy"]));

            #region Validate parameter & prevent SQL injection for ORDER clause
            $OrderBy = SetVariable("OrderBy", $this->Property["OrderBy"]);
            $AllowedOrderByColumn = array_merge([$this->Property["OrderBy"]], array_keys($this->Property["Table"]->Structure()["Column"]));
            if(!in_array($OrderBy, $AllowedOrderByColumn))$OrderBy = $AllowedOrderByColumn[0];
            
            $Order = strtoupper(SetVariable("Order", $this->Property["Order"]));
            if(!in_array($Order, ["ASC", "DESC"]))$Order = "ASC";
            
			$Page = intval(SetVariable("Page", 1));
			if($Page < 1)$Page = 1;

            $RecordsPerPage = intval(SetVariable("RecordsPerPage", $this->Property["RecordsPerPage"]));
			if($RecordsPerPage < 1)$Page = 15;
            #endregion Validate parameter & prevent SQL injection for ORDER clause

            $this->Property["VisibleRecordset"] = $this->Property["Table"]->Get(implode(" AND ", array_filter($this->Property["SearchSQL"])), "{$OrderBy} {$Order}", (($Page - 1) * ($RecordsPerPage)) + 1, $RecordsPerPage, null, null, $this->Property["Verbose"]);

			$this->Property[__FUNCTION__] = "
				" . HTML\UI\Datagrid(
					$this->Property["VisibleRecordset"],
					$this->Property["URL"] . (isset($SearchArgument) ? "&" . implode("&", $SearchArgument) : null) . "",
					$this->Property["Table"]->Count(),
					$this->Property["ListColumn"], // Columns to display
					"<img src=\"{$this->Property["IconURL"]}{$this->Property["LowercaseEntityName"]}.png\" alt=\"{$this->Property["Table"]->EntityName()}\" class=\"Icon\">" . ($this->Property["ListTitle"] ? $this->Property["ListTitle"] : $this->Property["Table"]->FormalName()) . "",
					$RecordsPerPage,
					$this->Property["Table"]->Structure()["Primary"][0],
					$this->Property["Action"],
					$this->Property["BaseURL"], // Base URL
					$this->Property["IconURL"], // Base URL for icons
					"Total of {$this->Property["Table"]->Count()} record(s) took " . round($this->Property["Table"]->LastDuration() * 1000, 0) . " ms",
					"
						" . (is_array($this->Property["SearchUIHTML"]) ? implode("", $this->Property["SearchUIHTML"]) : $this->Property["SearchUIHTML"]) . "<div class=\"ColumnWrapper\"></div>
						<div class=\"ButtonRow\">" . (is_array($this->Property["BatchActionHTML"]) ? implode(" ", $this->Property["BatchActionHTML"]) : $this->Property["BatchActionHTML"]) . "</div>
					",
					null,
					$this->Property["ListRowExpandURL"], // Expand URL
					null,
					null,
					null,
					$this->Property["DatagridCSSSelector"], // CSSSelector
                    null, 
                    null, 
                    null, 
                    $this->Property["ListRowCSSSelector"] // RowCSSSelector
				) . "
			";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function InputHTML($Header = null, $Footer = null){
        if(is_null($this->Property[__FUNCTION__])){
			if(is_array($this->Property["EntityID"]))$this->Property["EntityID"] = 0;

			unset($_POST["_Referer"]); // Reset referer URL form field

			$this->Property[__FUNCTION__] = "
				<div class=\"AlignCenter\">
					" . HTML\UI\Form(
						$this->Property["URL"], // Submission URL
						(is_array($this->Property["InputUIHTML"]) ? implode("", $this->Property["InputUIHTML"]) : $this->Property["InputUIHTML"]) .
							HTML\UI\Input("btnInput", null, null, true, INPUT_TYPE_HIDDEN) .
							HTML\UI\Input("{$this->Property["Table"]->Structure()["Primary"][0]}", null, $this->Property["EntityID"], true, INPUT_TYPE_HIDDEN),
						$this->Property["EntityID"] ? "Update" : "Insert", // Submit button caption
						$this->Property["EncryptionKey"], // Signature modifier
						"<img src=\"{$this->Property["IconURL"]}{$this->Property["LowercaseEntityName"]}.png\" alt=\"{$this->Property["Table"]->EntityName()}\" class=\"Icon\">" . ($this->Property["EntityID"] ? "Edit" : "Add new") . " " . strtolower($this->Property["InputTitle"] ? $this->Property["InputTitle"] : $this->Property["Table"]->FormalName()) . "", // Title
						is_null($Header) ? "Use the form below to add a new {$this->Property["LowercaseEntityName"]} record into the system." : $Header, // Header
						is_null($Footer) ? "Press the 'Insert' or 'Update' button to save the information." : $Footer, // Footer
						"All field(s) are required except marked optional.", // Status
						"frm{$this->Property["Table"]->EntityName()}Input" // ID
					) . "
				</div>
			";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ImportHTML(){
        if(is_null($this->Property[__FUNCTION__])){
			$this->Property[__FUNCTION__] = "
				<div class=\"AlignCenter\">
					" . HTML\UI\Form(
						$this->Property["URL"], // Submission URL
						"
							" . HTML\UI\Field(HTML\UI\Input("{$this->Property["Table"]->EntityName()}" . ($Caption = "Data") . "File", $this->Property["InputFullWidth"], null, true, INPUT_TYPE_FILE), "{$Caption}", false, false, $this->Property["FieldCaptionWidth"]) . "
							" . HTML\UI\Input("btn" . ($Caption = "Import") . "", $this->Property["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
						",
						"Import", // Submit button caption
						$this->Property["EncryptionKey"], // Signature modifier
						"<img src=\"{$this->Property["IconURL"]}{$this->Property["LowercaseEntityName"]}.png\" alt=\"{$this->Property["Table"]->EntityName()}\" class=\"Icon\">Import " . strtolower($this->Property["Table"]->FormalName()) . "", // Title
						"Use the form below to upload data file to import {$this->Property["LowercaseEntityName"]} from.", // Header
						"<a href=\"./document/sample/import/{$this->Property["LowercaseEntityName"]}.csv\">Download</a> sample CSV file", // Footer
						"All field(s) are required except marked optional.", // Status
						"frm{$this->Property["Table"]->EntityName()}Import" // ID
					) . "
				</div>
			";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function ExportHTML(){
        if(is_null($this->Property[__FUNCTION__])){
			$EntityIDList = is_array($this->Property["EntityID"]) ? implode(", ", $this->Property["EntityID"]) : null;
			foreach(array_keys($this->Property["Table"]->Get(null, null, 1, 1)[0]) as $Column)$ColumnOption[] = new Option($Column);

			$this->Property[__FUNCTION__] = "
				<div class=\"AlignCenter\">
					" . HTML\UI\Form(
						$this->Property["URL"], // Submission URL
						"
							" . HTML\UI\Field(HTML\UI\CheckboxGroup("" . ($Caption = "Column") . "", $ColumnOption), "{$Caption}", null, null, $this->Property["FieldCaptionWidth"], $this->Property["FieldContentFullWidth"]) . "
							" . HTML\UI\Field(HTML\UI\Select("" . ($Caption = "Format") . "", [new Option(IMPORT_TYPE_CSV), new Option(IMPORT_TYPE_TSV), new Option(IMPORT_TYPE_XML), new Option(IMPORT_TYPE_JSON)]), "{$Caption}", true, null, $this->Property["FieldCaptionWidth"]) . "
							" . HTML\UI\Field(HTML\UI\RadioGroup("" . ($Caption = "Header") . "", [new HTML\UI\Radio(1, "Yes"), new HTML\UI\Radio(0, "No")]), "{$Caption}", false, true) . "
							" . HTML\UI\Input("{$this->Property["Table"]->EntityName()}" . ($Caption = "ID") . "List", $this->Property["InputWidth"], $EntityIDList, true, INPUT_TYPE_HIDDEN) . "
							" . HTML\UI\Input("" . ($Caption = "SearchSQL") . "", $this->Property["InputWidth"], implode(" AND ", array_filter($this->Property["SearchSQL"])), true, INPUT_TYPE_HIDDEN) . "
							" . HTML\UI\Input("" . ($Caption = "Order") . "By", $this->Property["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
							" . HTML\UI\Input("" . ($Caption = "Order") . "", $this->Property["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
							" . HTML\UI\Input("btn" . ($Caption = "Export") . "", $this->Property["InputWidth"], null, true, INPUT_TYPE_HIDDEN) . "
						",
						"Export", // Submit button caption
						$this->Property["EncryptionKey"], // Signature modifier
						"<img src=\"{$this->Property["IconURL"]}{$this->Property["LowercaseEntityName"]}.png\" alt=\"{$this->Property["Table"]->EntityName()}\" class=\"Icon\">Export {$this->Property["LowercaseEntityName"]} data", // Title
						"Use the form below to export {$this->Property["LowercaseEntityName"]} data to the desired format.", // Header
						"Press the 'Export' button to save the information.", // Footer
						"All field(s) are required except marked optional.", // Status
						"frm{$this->Property["Table"]->EntityName()}Export" // ID
					) . "
				</div>
			";
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function LowercaseEntityName(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function EntityID(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function VisibleRecordset(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function SearchURLArgument($Field = [], $KeepSearchInputPrefix = null, $SearchInputPrefix = null){
        /*
            $Field = Array of URL argument(s) to generate URL for; Default is main columns from associated Database table
            $KeepSearchInputPrefix = Keep search input prefix with argument name
            $SearchInputPrefix = Prefix to use with argument name; Helps where SearchInputPrefix property is not set yet
        */

        if(is_null($this->Property[__FUNCTION__])){
            #region Set argumewnt default value
            if(is_null($Field))$Field = [];
            if(is_null($SearchInputPrefix))$SearchInputPrefix = is_null($this->Property["SearchInputPrefix"]) ? \sPHP::$Configuration["SearchInputPrefix"] : $this->Property["SearchInputPrefix"];
            if(is_null($KeepSearchInputPrefix))$KeepSearchInputPrefix = false;
            #endregion Set argumewnt default value

            #region Validate argument
            if(!is_array($Field))$Field = explode(",", str_replace(" ", "", $Field));
            if(!count($Field))$Field = array_diff(array_keys($this->Property["Table"]->Structure()["Column"]), explode(",", str_replace(" ", "", "UserIDInserted, UserIDUpdated, UserIDLocked, TimeInserted, TimeUpdated, TimeLocked")));

            if($KeepSearchInputPrefix)$KeepSearchInputPrefix = $SearchInputPrefix;
            #endregion Validate argument
    
            #region Generate URL
            $URL = [];

            foreach(array_filter($Field) as $Argument){
				$SearchArgument = "{$SearchInputPrefix}{$Argument}"; //DebugDump($SearchArgument);

				if(isset($_POST[$SearchArgument]) && strlen($_POST[$SearchArgument])){
					$URL[] = "{$KeepSearchInputPrefix}{$Argument}=" . urlencode($_POST[$SearchArgument]) . "";
				}
			}

            $Result = $this->Property[__FUNCTION__] = implode("&", $URL);
            #endregion Generate URL
        }
        else{
            $Result = $this->Property[__FUNCTION__];
        }

        return $Result;
    }
    #endregion Property
}
?>