<?php
/*
    Name:           Table
    Purpose:        Database table object
    Author:         Broken Arrow (SKJoy2001@GMail.Com);
    Date modified:  June 12, 2018 10:51 AM
*/

namespace sPHP\Database;

class Table{
    private $Property = [
        "FormalName"				=>	null,
        "Alias"						=>	null,
		"EntityName"				=>	null,
		"UploadPath"				=>	null,
		"SQLSELECTPath"				=>	null,
        "Database"					=>	null,
        "Prefix"					=>	null,
        "Name"						=>	null,
        "SELECTStatement"			=>	null,
		"BeforeGet"					=>	null,
		"AfterGet"					=>	null,
		"BeforePut"					=>	null,
		"AfterPut"					=>	null,
		"BeforeInsert"				=>	null,
		"AfterInsert"				=>	null,
		"BeforeUpdate"				=>	null,
		"AfterUpdate"				=>	null,
		"BeforeRemove"				=>	null,
		"AfterRemove"				=>	null,
		"LastDuration"				=>	0,
		"Duration"					=>	0,
		"Count"						=>	null,
		"QueryHistory"				=>	[],
		"Structure"					=>	null,
    ];

	#region Variable
	//private $Structure = null;
	#endregion Variable

    #region Method
    public function __construct($FormalName = null, $Alias = null, $EntityName = null, $UploadPath = null, $SQLSELECTPath = null, $Database = null, $Prefix = null, $Name = null, $SELECTStatement = null, $BeforeGet = null, $AfterGet = null, $BeforePut = null, $AfterPut = null, $BeforeInsert = null, $AfterInsert = null, $BeforeUpdate = null, $AfterUpdate = null, $BeforeRemove = null, $AfterRemove = null){
        // Set property values from arguments passed during object instantiation
        foreach(get_defined_vars() as $ArgumentName=>$ArgumentValue)if(!is_null($ArgumentValue) && array_key_exists($ArgumentName, $this->Property))$this->$ArgumentName($ArgumentValue);

        return true;
    }

    public function __destruct(){

        return true;
    }

	public function Get($WHERE = null, $ORDERBY = null, $From = null, $Count = null, $GROUPBY = null, $GroupField = null, $Verbose = false){
		$SQL = "" .
			($GROUPBY ? "SELECT {$GroupField} FROM {$this->Property["Prefix"]}{$this->Property["Name"]} AS {$this->Property["Alias"]}" : $this->SELECTStatement()) . "" .
			($WHERE ? " WHERE {$WHERE}" : null) . "" .
			($GROUPBY ? " GROUP BY {$GROUPBY}" : null) . "" .
		"";

		if($this->Property["Database"]->Type() == \sPHP\DATABASE_TYPE_MYSQL){
			$ORDERBYClause = "" . ($ORDERBY ? " ORDER BY {$ORDERBY}" : null) . "";
			$LIMITClause = "" .  ($From && $Count ? " LIMIT " . ($From - 1) . ", {$Count}" : null) . "";
			$WholeSQL = "{$SQL} {$ORDERBYClause} {$LIMITClause}"; //var_dump($WholeSQL);
		}
		elseif($this->Property["Database"]->Type() == \sPHP\DATABASE_TYPE_MSSQL){
			if($From){
				if(!$ORDERBY)$ORDERBY = "{$this->Structure()[count($this->Structure()["VarChar"]) ? "VarChar" : "Primary"][0]} ASC";

				foreach(explode(",", $ORDERBY) as $OrderByPart){
					$OrderByPart = trim($OrderByPart);
					$NewOrderByPart[] = ($MarkerPosition = strpos($OrderByPart, ".")) ? substr($OrderByPart, $MarkerPosition + 1) : $OrderByPart;
				}

				$WholeSQL = "
					SELECT ___OuterWrapper.*

					FROM (
						SELECT ___InnerWrapper.*, ROW_NUMBER() OVER(ORDER BY " . implode(", ", $NewOrderByPart) . ") AS ___RowNumber
						FROM ({$SQL}) AS ___InnerWrapper
					) AS ___OuterWrapper

					" . ($From ? "WHERE ___OuterWrapper.___RowNumber >= {$From}" . ($Count ? " AND ___OuterWrapper.___RowNumber <= " . ($From + $Count - 1) . "" : null) . "" : null) . "
				";
			}
			else{
				$ORDERBYClause = "" . ($ORDERBY ? " ORDER BY {$ORDERBY}" : null) . "";
				$WholeSQL = "{$SQL} {$ORDERBYClause}"; //var_dump($WholeSQL);
			}
		}
		else{
			die("Database type not supported!");
		}

		$Result = false;
		$this->Property["Count"] = 0;

		if(is_null($this->Property["BeforeGet"]) || $this->Property["BeforeGet"]($WholeSQL)){
			if($Verbose)print "<div class=\"MessageBox\"><div class=\"Container\"><div class=\"Title\">" . __CLASS__ . "-&gt;" . __FUNCTION__ . "</div><div class=\"Content\"><pre class=\"Code\">" . trim($SQL) . "</pre></div></div></div>";
			//var_dump($WholeSQL);
			//if($this->Property["Database"]->Query($WholeSQL) && count($this->Property["Database"]->Recordset())){
			$QueryResult = $this->Property["Database"]->Query($WholeSQL);

			if(is_array($QueryResult)){
				$this->Property["LastDuration"] = $this->Property["Database"]->LastDuration();
				$this->Property["Duration"] = $this->Property["Duration"] + $this->Property["LastDuration"];
				if($this->Property["Database"]->KeepQueryHistory())$this->Property["QueryHistory"][] = $this->Property["Database"]->QueryHistory()[count($this->Property["Database"]->QueryHistory()) - 1];
				//\sPHP\DebugDump($this->Property["Database"]->Recordset()); //exit;
				$Result = count($QueryResult) ? $QueryResult[0] : [];
				$this->Property["Count"] = $From ? $this->Property["Database"]->Query("SELECT COUNT(1) AS Count FROM ({$SQL}) AS ___Recordset", null, null, true)[0][0]["Count"] : count($Result);
			}
			else{
				//file_put_contents(__DIR__ . "/debug.json", json_encode(["QueryResult" => $QueryResult, "WholeSQL" => $WholeSQL, ]));
			}

			if(!is_null($this->Property["AfterGet"]))$Result = $this->Property["AfterGet"]($Result, $WholeSQL);
		}

		return $Result;
	}

	public function Put($Data, $WHERE = null, $Field = null, $Verbose = false, $sPHPColumn = true, $IgnoreINSERTError = false){
		if(!is_array($Data[key($Data)]))$Data = [$Data]; // Convert single record to multi record format
		if($WHERE)$Data = [$Data[key($Data)]]; // Take only one row for UPDATE mode
		$Field = array_intersect($Field ? \sPHP\ListToArray($Field) : array_keys($Data[0]), array_keys($this->Structure()["Column"]));

		foreach($Data as $Row){
			$Row = array_values($Row);
			$INSERTColumnSQL = [];
			//var_dump($Row);
			foreach($Field as $ColumnIndex => $Column){
				#region DEBUG
				if(is_array($Row[$ColumnIndex])){
					file_put_contents(__DIR__ . "/debug_table_RowColumnIndexIsNotString.json", json_encode([
						"Row[{$ColumnIndex}]" => $Row[$ColumnIndex], 
						"BackTrace" => debug_backtrace(), 
					]));
				}
				#endregion DEBUG

				if(!strlen($Row[$ColumnIndex]) && in_array($Column, $this->Structure()["Nullable"])){
					$Value = "NULL";
				}
				else{
					//var_dump($Column, in_array($Column, array_merge($this->Structure()["Date"], $this->Structure()["Time"], $this->Structure()["DateTime"], $this->Structure()["String"])), array_merge($this->Structure()["Date"], $this->Structure()["Time"], $this->Structure()["DateTime"], $this->Structure()["String"]));
					if(in_array($Column, array_merge($this->Structure()["Date"], $this->Structure()["Time"], $this->Structure()["DateTime"], $this->Structure()["String"]))){
						$Value = "'" . str_replace(["'", "\\"], ["''", "\\\\"], $Row[$ColumnIndex]) . "'";
					}
					else{
						$Value = in_array($Column, $this->Structure()["Integer"]) ? intval($Row[$ColumnIndex]) : floatval($Row[$ColumnIndex]);
					}
				}

				//$Value = isset($Row[$ColumnIndex]) ? (in_array($Column, array_merge($this->Structure()["String"], $this->Structure()["Date"], $this->Structure()["Time"], $this->Structure()["DateTime"])) ? "'" . str_replace(["'", "\\"], ["''", "\\\\"], $Row[$ColumnIndex]) . "'" : (in_array($Column, $this->Structure()["Integer"]) ? intval($Row[$ColumnIndex]) : floatval($Row[$ColumnIndex]))) : "NULL";
				//if($Value == "''" && in_array($Column, array_merge($this->Structure()["Date"], $this->Structure()["Time"], $this->Structure()["DateTime"])))$Value = "NULL";

				$INSERTColumnSQL[] = $Value;
				$UPDATEColumnSQL[] = "{$Column} = {$Value}";
			}

			if($sPHPColumn && in_array("UserIDInserted", $this->Structure()["Number"])){
				$INSERTColumnSQL[] = $_SESSION["User"]->ID() ? $_SESSION["User"]->ID() : "NULL";
				$INSERTColumnSQL[] = "'" . date("Y-m-d H:i:s") . "'";

				$UPDATEColumnSQL[] = "UserIDUpdated = " . ($_SESSION["User"]->ID() ? $_SESSION["User"]->ID() : "NULL") . "";
				$UPDATEColumnSQL[] = "TimeUpdated = '" . date("Y-m-d H:i:s") . "'";
			}

			$INSERTRowSQL[] = "(" . implode(", ", $INSERTColumnSQL) . ")";
			$UPDATERowSQL = "" . implode(", ", $UPDATEColumnSQL) . "";
		}

		if($sPHPColumn && in_array("UserIDInserted", $this->Structure()["Number"])){
			if($WHERE){
				$Field[] = "UserIDUpdated";
				$Field[] = "TimeUpdated";
			}
			else{
				$Field[] = "UserIDInserted";
				$Field[] = "TimeInserted";
			}
		}

		$INSERTSQL = "INSERT" . ($IgnoreINSERTError ? " IGNORE" : null) . " INTO {$this->Property["Prefix"]}{$this->Property["Name"]} (" . implode(", ", $Field) . ") VALUES " . implode(", ", $INSERTRowSQL) . "";
		$UPDATESQL = "UPDATE {$this->Property["Prefix"]}{$this->Property["Name"]} SET {$UPDATERowSQL} WHERE {$WHERE}";
		$ModeName = $WHERE ? "Update" : "Insert";
		$ApplicableSQL = $WHERE ? $UPDATESQL : $INSERTSQL;

		if(is_null($this->Property["BeforePut"]) || $this->Property["BeforePut"]($Data, $Field)){
			if(is_null($this->Property["Before{$ModeName}"]) || $this->Property["Before{$ModeName}"]($ApplicableSQL, $Data, $Field)){
				//var_dump($ApplicableSQL);
				$Result = $this->Property["Database"]->Query($ApplicableSQL, null, $Verbose);
				if($Result !== false)$Result = true; // Let the Result contain the status of the operation
				if(!is_null($this->Property["After{$ModeName}"]))$Result = $this->Property["After{$ModeName}"]($Result, $ApplicableSQL, $Data, $Field);
			}else{
				$Result = false;
			}

            $DatabaseQueryHistoryCount = count($this->Property["Database"]->QueryHistory());
			if($DatabaseQueryHistoryCount)$this->Property["QueryHistory"][] = $this->Property["Database"]->QueryHistory()[$DatabaseQueryHistoryCount - 1];

			if(!is_null($this->Property["AfterPut"]))$Result = $this->Property["AfterPut"]($Result, $Data, $Field);
		}
        else{
            $Result = false;
        }

		return $Result;
	}

	public function Remove($WHERE = null, $FileField = null, $Verbose = false){
		#region Delete files
		$FileField = \sPHP\ListToArray($FileField); // Convert argument file field list to array

		// If no file field list is supplied with, generate the default list
		if(!count($FileField))foreach($this->Structure()["String"] as $Column)foreach(\sPHP\ListToArray("File, Document, Image, Icon, Logo, Picture, Thumbnail") as $Suffix)if(substr($Column, strlen($Column) - strlen($Suffix)) == $Suffix)$FileField[] = $Column;

		// Keep the file field list withing actual table columns
		$FileField = array_intersect($FileField, $this->Structure()["String"]);

		foreach($FileField as $Column)$WHEREClause[] = "{$Column} > ''"; // Filter records with file values
		if($WHERE)$WHEREClause[] = $WHERE; // Add up the supplied WHERE clause

		if(count($FileField)){ // We have some column with file field
			// Check and fetch records from table
			if($Recordset = $this->Database()->Query("SELECT " . implode(", ", $FileField) . " FROM {$this->Property["Prefix"]}{$this->Property["Name"]}" . (isset($WHEREClause) ? " WHERE " . implode(" AND ", $WHEREClause) . "" : null) . "", null, null)){
				if(is_array($Recordset))foreach($Recordset[0] as $Record){ // Iterate through records and file fields
					foreach($FileField as $Column){ // Iterate through each file field
						// Check if file from file field exists
						if($Record[$Column] && file_exists($ExistingFile = "{$this->Property["UploadPath"]}{$Record[$Column]}")){
							unlink($ExistingFile); // Delete the file
						}
					}
				}
			}
		}
		#endregion Delete files

		$SQL = "DELETE FROM {$this->Property["Prefix"]}{$this->Property["Name"]}" . ($WHERE ? " WHERE {$WHERE}" : null) . "";

		if(is_null($this->Property["BeforeRemove"]) || $this->Property["BeforeRemove"]($SQL)){
			$Result = $this->Property["Database"]->Query($SQL, null, $Verbose);
			if(!is_null($this->Property["AfterRemove"]))$Result = $this->Property["AfterRemove"]($Result, $SQL);
		}else{$Result = false;}

		return $Result;
	}

    public function Import($File = null, $Structure = null, $Header = null, $DataType = null, $Data = null, $AfterParse = null, $Resource = []){
		$Result = false; 
		$Structure = array_filter($Structure);

		if(is_null($Header))$Header = true;
		if(is_null($DataType))$DataType = \sPHP\IMPORT_TYPE_CSV;

        $Data = $File ? file_get_contents($File) : $Data;
		$DatabaseTableColumnName = array_keys($this->Structure()["Column"]);

		if($DataType == \sPHP\IMPORT_TYPE_CSV || $DataType == \sPHP\IMPORT_TYPE_TSV){
			$Separator = $DataType == \sPHP\IMPORT_TYPE_CSV ? "," : "	";
			foreach(explode("\n", $Data) as $Record)$Recordset[] = explode($Separator, trim($Record)); // Avoid trailing nonprintable character!

			if($Header){ // Header row exists
				$Field = $Recordset[0]; //var_dump($Field); // Get column name array from header row
				array_shift($Recordset); // Remove header row
			}else{
				$Field = array_keys($Recordset[0]);
			}

			if($AfterParse)$AfterParse($Recordset, $Field, $Resource); // Call the AfterParse callback function
		}

		foreach($Structure as $ThisStructure){
			if(in_array($ThisStructure->Column(), $DatabaseTableColumnName))$ColumnNameSQL[] = $ThisStructure->Column();
			$ColumnIsString[$ThisStructure->Column()] = in_array($ThisStructure->Column(), array_merge($this->Property["Structure"]["String"], $this->Structure()["DateTime"], $this->Structure()["Date"], $this->Structure()["Time"])) ? true : false;
		}

		foreach($Recordset as $Record){ //var_dump($Record);
			if(count($Record) >= count($Field)){ // Do we really need to restrict by the number of Structure columns? so made it TRUE
				foreach($Field as $Key => $Value)$Record[trim($Value)] = $Record[$Key];
				$ColumnData = [];

				foreach($Structure as $ThisStructure){
					if(in_array($ThisStructure->Column(), $DatabaseTableColumnName)){
						$ThisColumnData = isset($Record[$ThisStructure->Field()]) ? trim($Record[$ThisStructure->Field()]) : $ThisStructure->Value();
						if(strlen($ThisColumnData) && substr($ThisColumnData, 0, 1) == "\"" && substr($ThisColumnData, strlen($ThisColumnData) - 1, 1) == "\"")$ThisColumnData = substr($ThisColumnData, 1, strlen($ThisColumnData) - 2);

						if(!is_null($ThisStructure->RelationTable())){
							$ThisStructure->Value($ThisColumnData);
							$ThisColumnData = "({$ThisStructure->RelationSQL()})";
						}
						else{
							$ThisColumnData = str_replace(["\\", "'"], ["\\\\", "''"], $ThisColumnData);
						}

						if($ColumnIsString[$ThisStructure->Column()])$ThisColumnData = "'{$ThisColumnData}'";
						$ColumnData[] = $ThisColumnData;
					}
				} //var_dump($ColumnData);

				$FieldSQL[] = "(" . implode(", ", $ColumnData) . ")";
			}
		} //var_dump($FieldSQL);
		
		$this->Property["Database"]->Query($SQL = "INSERT IGNORE INTO {$this->Property["Prefix"]}{$this->Property["Name"]} (" . implode(", ", $ColumnNameSQL) . ") VALUES \n" . implode(", \n", $FieldSQL) . "", null, false);
		//print "<div class=\"Code\">{$SQL}</div>";

        return $Result;
    }

	public function Export($Column, $Header = null, $Format = null, $File = null, $WHEREClause = null, $Order = null){
		if(!is_array($Column))$Column = explode(",", str_replace(" ", null, $Column));
		if(is_null($Header))$Header = true;
		if(is_null($Format))$Format = \sPHP\IMPORT_TYPE_CSV;

		if($Header)$Line[] = $Header === true ? implode(",", $Column) : $Header;

		foreach($this->Get(is_array($WHEREClause) ? implode(" AND ", array_filter($WHEREClause)) : $WHEREClause, $Order) as $Record){
			$Word = [];
			foreach($Column as $ThisColumn)$Word[] = "\"{$Record[$ThisColumn]}\"";
			$Line[] = implode(",", $Word);
		}

		$Result = implode("\n", $Line);
		if($File)file_put_contents($File, $Result);

		return $Result;
	}
    #endregion Method

    #region Property
    public function FormalName($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(is_null($this->Property["EntityName"])){
				foreach(explode(" ", $this->Property[__FUNCTION__]) as $Word)$this->Property["EntityName"][] = "" . strtoupper(substr($Word, 0, 1)) . substr($Word, 1) . "";
				$this->Property["EntityName"] = implode(null, $this->Property["EntityName"]);
			}

			if(is_null($this->Property["Name"]))$this->Property["Name"] = strtolower($this->Property["EntityName"]);
			if(is_null($this->Property["UploadPath"]))$this->Property["UploadPath"] = "./upload/{$this->Property["Name"]}/";

			if(is_null($this->Property["Alias"])){
				foreach(explode(" ", $this->Property["FormalName"]) as $Word)$Alias[] = strtoupper(substr($Word, 0, 1));
				$this->Property["Alias"] = implode(null, $Alias);
			}

			$this->Property["SELECTStatement"] = null;
			$this->Property["Structure"] = null;

			$Result = true;
        }

        return $Result;
    }

    public function Alias($Value = null){
		if(is_null($Value)){
			$Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["SELECTStatement"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function EntityName($Value = null){
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

    public function SQLSELECTPath($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function Database($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			if(is_null($this->Property["Prefix"]))$this->Property["Prefix"] = $this->Property[__FUNCTION__]->TablePrefix();

			$this->Property["Structure"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function Prefix($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["SELECTStatement"] = null;
			$this->Property["Structure"] = null;

			$Result = true;
        }

        return $Result;
    }

    public function Name($Value = null){
		if(is_null($Value)){
			$Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$this->Property["SELECTStatement"] = null;
			$this->Property["Structure"] = null;

            $Result = true;
        }

        return $Result;
    }

    public function SELECTStatement($Value = null){
        if(is_null($Value)){
			if(is_null($this->Property[__FUNCTION__])){
				$this->Property[__FUNCTION__] = "SELECT {$this->Property["Alias"]}.* \n\nFROM {$this->Property["Prefix"]}{$this->Property["Name"]} AS {$this->Property["Alias"]} ";

				if($this->Property["SQLSELECTPath"]){
					if(file_exists($SQLFile = "{$this->Property["SQLSELECTPath"]}{$this->Property["Name"]}.sql")){
						$this->Property[__FUNCTION__] = str_replace(explode(",", str_replace(" ", null, "{PREFIX}, {NAME}, {ALIAS}, {ENTITY}")), [$this->Property["Prefix"], $this->Property["Name"], $this->Property["Alias"], $this->Property["EntityName"]], file_get_contents($SQLFile));
					}
					else{
						$SQL = "SELECT			{ALIAS}.*, \n				CONCAT({ALIAS}." . (count($this->Structure()["String"]) ? $this->Structure()["String"][0] : $this->Structure()["Primary"][0]) . ", '') AS {ENTITY}LookupCaption, \n				'' AS _Other\n\nFROM			{PREFIX}{NAME} AS {ALIAS}\n	/*LEFT JOIN		X AS Y ON Y.YID = {ALIAS}.YID*/\n";
						file_put_contents("{$this->Property["SQLSELECTPath"]}{$this->Property["Name"]}.sql", $SQL ? $SQL : $this->Property["SELECTStatement"]);
					}
				}
			}
			//var_dump($this->Property[__FUNCTION__]);
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

			$Result = true;
        }

        return $Result;
    }

    public function BeforeGet($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AfterGet($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BeforePut($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AfterPut($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BeforeInsert($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AfterInsert($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BeforeUpdate($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AfterUpdate($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function BeforeRemove($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function AfterRemove($Value = null){
        if(is_null($Value)){
            $Result = $this->Property[__FUNCTION__];
        }
        else{
            $this->Property[__FUNCTION__] = $Value;

            $Result = true;
        }

        return $Result;
    }

    public function LastDuration(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Duration(){
		$Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Count(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function QueryHistory(){
        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }

    public function Structure(){
		if(is_null($this->Property[__FUNCTION__])){
			$this->Property[__FUNCTION__]["Primary"] = $this->Property[__FUNCTION__]["Nullable"] = $this->Property[__FUNCTION__]["AutoIncrement"] = $this->Property[__FUNCTION__]["Number"] = $this->Property[__FUNCTION__]["Integer"] = $this->Property[__FUNCTION__]["Float"] = $this->Property[__FUNCTION__]["String"] = $this->Property[__FUNCTION__]["VarChar"] = $this->Property[__FUNCTION__]["Text"] = $this->Property[__FUNCTION__]["Date"] = $this->Property[__FUNCTION__]["Time"] = $this->Property[__FUNCTION__]["DateTime"] = [];

			if($this->Property["Database"]->Type() == \sPHP\DATABASE_TYPE_MYSQL){
				$this->Property[__FUNCTION__]["Column"] = $this->Property["Database"]->Query("SHOW COLUMNS FROM {$this->Property["Prefix"]}{$this->Property["Name"]}")[0];

				foreach($this->Property[__FUNCTION__]["Column"] as $Column){
					$ColumnStructure[$Column["Field"]] = $Column;

					if(($MarkerPosition = strpos($Column["Type"], "(")) === false){
						$ColumnStructure[$Column["Field"]]["Length"] = false;
						$ColumnStructure[$Column["Field"]]["Type"] = strtoupper($Column["Type"]);
					}
					else{
						$ColumnStructure[$Column["Field"]]["Length"] = intval(substr($Column["Type"], $MarkerPosition + 1, strlen($Column["Type"]) - $MarkerPosition - 2));
						$ColumnStructure[$Column["Field"]]["Type"] = strtoupper(substr($Column["Type"], 0, $MarkerPosition));
					}

					if(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 8) == "VARCHAR"){
						$this->Property[__FUNCTION__]["String"][] = $ColumnStructure[$Column["Field"]]["Field"];
						$this->Property[__FUNCTION__]["VarChar"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}
					elseif(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 8) == "TEXT"){
						$this->Property[__FUNCTION__]["String"][] = $ColumnStructure[$Column["Field"]]["Field"];
						$this->Property[__FUNCTION__]["Text"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}
					elseif(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 8) == "DATETIME"){
						$this->Property[__FUNCTION__]["DateTime"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}
					elseif(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 8) == "DATE"){
						$this->Property[__FUNCTION__]["Date"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}
					elseif(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 8) == "TIME"){
						$this->Property[__FUNCTION__]["Time"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}
					elseif(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 8) == "TINYINT"){
						$this->Property[__FUNCTION__]["Number"][] = $ColumnStructure[$Column["Field"]]["Field"];
						$this->Property[__FUNCTION__]["Integer"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}
					elseif(substr($ColumnStructure[$Column["Field"]]["Type"], 0, 4) == "INT"){
						$this->Property[__FUNCTION__]["Number"][] = $ColumnStructure[$Column["Field"]]["Field"];
						$this->Property[__FUNCTION__]["Integer"][] = $ColumnStructure[$Column["Field"]]["Field"];
					}

					if($Column["Key"] == "PRI")$this->Property[__FUNCTION__]["Primary"][] = $Column["Field"];
					if($Column["Null"] == "YES")$this->Property[__FUNCTION__]["Nullable"][] = $Column["Field"];
					if($Column["Extra"] == "auto_increment")$this->Property[__FUNCTION__]["AutoIncrement"][] = $Column["Field"];
				}

				$this->Property[__FUNCTION__]["Column"] = $ColumnStructure;
			}
			elseif($this->Property["Database"]->Type() == \sPHP\DATABASE_TYPE_MSSQL){
				$this->Property[__FUNCTION__]["Column"] = $this->Property["Database"]->Query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'dbo' AND TABLE_NAME = '{$this->Property["Prefix"]}{$this->Property["Name"]}'")[0];

				foreach($this->Property[__FUNCTION__]["Column"] as $ColumnCounter=>$Column){
					$ColumnStructure[$Column["COLUMN_NAME"]] = $Column;
					$ColumnStructure[$Column["COLUMN_NAME"]]["Type"] = strtoupper($Column["DATA_TYPE"]);

					if(in_array($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], \sPHP\ListToArray("INT, TINYINT"))){
						$ColumnStructure[$Column["COLUMN_NAME"]]["Length"] = $Column["NUMERIC_PRECISION"];
					}
					else{
						$ColumnStructure[$Column["COLUMN_NAME"]]["Length"] = $Column["CHARACTER_MAXIMUM_LENGTH"];
					}

					if(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 8) == "VARCHAR"){
						$this->Property[__FUNCTION__]["String"][] = $Column["COLUMN_NAME"];
						$this->Property[__FUNCTION__]["VarChar"][] = $Column["COLUMN_NAME"];
					}
					elseif(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 8) == "TEXT"){
						$this->Property[__FUNCTION__]["String"][] = $Column["COLUMN_NAME"];
						$this->Property[__FUNCTION__]["Text"][] = $Column["COLUMN_NAME"];
					}
					elseif(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 8) == "DATETIME"){
						$this->Property[__FUNCTION__]["DateTime"][] = $Column["COLUMN_NAME"];
					}
					elseif(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 8) == "DATE"){
						$this->Property[__FUNCTION__]["Date"][] = $Column["COLUMN_NAME"];
					}
					elseif(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 8) == "TIME"){
						$this->Property[__FUNCTION__]["Time"][] = $Column["COLUMN_NAME"];
					}
					elseif(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 8) == "TINYINT"){
						$this->Property[__FUNCTION__]["Number"][] = $Column["COLUMN_NAME"];
						$this->Property[__FUNCTION__]["Integer"][] = $Column["COLUMN_NAME"];
					}
					elseif(substr($ColumnStructure[$Column["COLUMN_NAME"]]["Type"], 0, 4) == "INT"){
						$this->Property[__FUNCTION__]["Number"][] = $Column["COLUMN_NAME"];
						$this->Property[__FUNCTION__]["Integer"][] = $Column["COLUMN_NAME"];
					}
					else{

					}

					if($Column["IS_NULLABLE"] == "YES")$this->Property[__FUNCTION__]["Nullable"][] = $Column["COLUMN_NAME"];
				}

				foreach($this->Property["Database"]->Query("
					SELECT *
					FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS AS TC
					LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE AS KCU ON KCU.CONSTRAINT_NAME = TC.CONSTRAINT_NAME
					WHERE TC.CONSTRAINT_TYPE = 'PRIMARY KEY' AND TC.TABLE_SCHEMA = 'dbo' AND TC.TABLE_NAME = '{$this->Property["Prefix"]}{$this->Property["Name"]}'
				")[0] as $ColumnInformation)$this->Property[__FUNCTION__]["Primary"][] = $ColumnInformation["COLUMN_NAME"];

				foreach($this->Property["Database"]->Query("
					SELECT *
					FROM INFORMATION_SCHEMA.COLUMNS
					WHERE COLUMNPROPERTY(object_id(TABLE_SCHEMA + '.' + TABLE_NAME), COLUMN_NAME, 'IsIdentity') = 1 AND TABLE_SCHEMA = 'dbo' AND TABLE_NAME = '{$this->Property["Prefix"]}{$this->Property["Name"]}'
				")[0] as $ColumnInformation)$this->Property[__FUNCTION__]["AutoIncrement"][] = $ColumnInformation["COLUMN_NAME"];

				$this->Property[__FUNCTION__]["Column"] = $ColumnStructure;
			}
			else{
				die("Database type not supported!");
			}
		}

        $Result = $this->Property[__FUNCTION__];

        return $Result;
    }
    #endregion Property

	#region Private function
	private function __AnalyzeStructure(){
		$Result = true;
		//var_dump($this->Structure()); exit;
		return $Result;
	}
	#endregion Private function
}
?>