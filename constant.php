<?php
/*
    Name:           Constant
    Purpose:        Framework based global constant library
    Author:         Broken Arrow (SKJoy2001@GMail.Com)
    Created:  		May, 30, 2018 05:30 PM
    Modified:  		Mon, 30 Jul 2018 17:40:00 GMT+06:00
*/

namespace sPHP;

const CHART_TYPE_AREA = "AREA";
const CHART_TYPE_BAR = "BAR";
const CHART_TYPE_LINE = "LINE";

const IMPORT_TYPE_CSV = "CSV";
const IMPORT_TYPE_TSV = "TSV";
const IMPORT_TYPE_XML = "XML";
const IMPORT_TYPE_JSON = "JSON";

const ALIGN_LEFT = "LEFT";
const ALIGN_CENTER = "CENTER";
const ALIGN_RIGHT = "RIGHT";

const DATA_TYPE_PHP_DATETIME = "PHP_DATETIME";

#region Field type
const FIELD_TYPE_TEXT = "TEXT";
const FIELD_TYPE_NUMBER = "NUMBER";
const FIELD_TYPE_INTEGER = "INTEGER";
const FIELD_TYPE_DATE = "DATE";
const FIELD_TYPE_TIME = "TIME";
const FIELD_TYPE_SHORTDATE = "SHORTDATE";
const FIELD_TYPE_LONGDATE = "LONGDATE";
const FIELD_TYPE_DATETIME = "DATETIME";
const FIELD_TYPE_EMAIL = "EMAIL";
const FIELD_TYPE_PHONE = "PHONE";
const FIELD_TYPE_ICON = "ICON";
const FIELD_TYPE_ICONURL = "ICONURL";
const FIELD_TYPE_GOOGLEMAPS = "GOOGLEMAPS";
const FIELD_TYPE_PICTURE = "PICTURE";
const FIELD_TYPE_PICTUREURL = "PICTUREURL";
const FIELD_TYPE_BOOLEANICON = "BOOLEANICON";
const FIELD_TYPE_URL = "URL";
const FIELD_TYPE_URLICON = "URLICON";
const FIELD_TYPE_COLOR = "COLOR";
//const FIELD_TYPE_TEMPLATE = "TEMPLATE"; // Apparently thought to be used with Column template, but implemented without it
#endregion Field type

const DATABASE_TYPE_MSACCESS = "MSACCESS";
const DATABASE_TYPE_MSSQL = "MSSQL";
const DATABASE_TYPE_MYSQL = "MYSQL";

#region Validation type
const VALIDATION_TYPE_ALPHABETIC = "ALPHABETIC";
const VALIDATION_TYPE_EMAIL = "EMAIL";
const VALIDATION_TYPE_PHONE = "PHONE";
const VALIDATION_TYPE_NUMBER = "NUMBER";
const VALIDATION_TYPE_NUMERIC = "NUMERIC";
const VALIDATION_TYPE_INTEGER = "INTEGER";
const VALIDATION_TYPE_FLOAT = "FLOAT";
const VALIDATION_TYPE_POSITIVE = "POSITIVE";
const VALIDATION_TYPE_NEGATIVE = "NEGATIVE";
const VALIDATION_TYPE_NONPOSITIVE = "NONPOSITIVE";
const VALIDATION_TYPE_NONNEGATIVE = "NONNEGATIVE";
const VALIDATION_TYPE_DATE = "DATE";
const VALIDATION_TYPE_URL = "URL";
#endregion Validation type

#region Input type
const INPUT_TYPE_TEXT = "TEXT";
const INPUT_TYPE_TEXTAREA = "TEXTAREA";
const INPUT_TYPE_CHECKBOX = "CHECKBOX";
const INPUT_TYPE_RICHTEXTAREA = "RICHTEXTAREA";
const INPUT_TYPE_EMAIL = "EMAIL";
const INPUT_TYPE_PASSWORD = "PASSWORD";
const INPUT_TYPE_NUMBER = "NUMBER";
const INPUT_TYPE_DATE = "DATE";
const INPUT_TYPE_TIME = "TIME";
const INPUT_TYPE_MONTH = "MONTH";
const INPUT_TYPE_PHONE = "PHONE";
const INPUT_TYPE_URL = "URL";
const INPUT_TYPE_COLOR = "COLOR";
const INPUT_TYPE_FILE = "FILE";
const INPUT_TYPE_HIDDEN = "HIDDEN";
#endregion Input type

const BUTTON_TYPE_BUTTON = "BUTTON";
const BUTTON_TYPE_RESET = "RESET";
const BUTTON_TYPE_SUBMIT = "SUBMIT";

#region Document type
const DOCUMENT_TYPE_CSS = "CSS";
const DOCUMENT_TYPE_CSV = "CSV";
const DOCUMENT_TYPE_HTML = "HTML";
const DOCUMENT_TYPE_JAVASCRIPT = "JS";
const DOCUMENT_TYPE_JSON = "JSON";
const DOCUMENT_TYPE_PDF = "PDF";
const DOCUMENT_TYPE_PICTURE_BMP = "BMP";
const DOCUMENT_TYPE_PICTURE_JPG = "JPG";
const DOCUMENT_TYPE_PICTURE_JPEG = "JPEG";
const DOCUMENT_TYPE_PICTURE_GIF = "GIF";
const DOCUMENT_TYPE_PICTURE_PNG = "PNG";
const DOCUMENT_TYPE_TXT = "TXT";
const DOCUMENT_TYPE_XML = "XML";
#endregion Document type

const CHARACTER_SET_UTF8 = "UTF-8";
const CHARACTER_SET_NONE = null;

const HTTP_STATUS_CODE_OK = 200;
const HTTP_STATUS_CODE_FORBIDDEN = 403;
const HTTP_STATUS_CODE_NOT_FOUND = 404;

const OUTPUT_BUFFER_MODE_HEADER = "HEADER";
const OUTPUT_BUFFER_MODE_MAIN = "MAIN";
const OUTPUT_BUFFER_MODE_TEMPLATE = "TEMPLATE";
const OUTPUT_BUFFER_MODE_ALL = "ALL";

const PATH_TEMP = "./temp/";

const NOTIFICATION_TYPE_EMAIL = "EMAIL";
const NOTIFICATION_TYPE_MOBILE_SMS = "MOBILE_SMS";
const NOTIFICATION_TYPE_APP = "APP";

const NOTIFICATION_SOURCE_SYSTEM = "SYSTEM";
const NOTIFICATION_SOURCE_MANUAL = "MANUAL";

const CRON_JOB_TYPE_PHP = "PHP";
const CRON_JOB_TYPE_URL = "URL";
const CRON_JOB_TYPE_SHELL = "SHELL";
const CRON_JOB_TYPE_SHELL_NOWAIT = "SHELL_NOWAIT";

const IMAGE_TYPE_GIF = "GIF";
const IMAGE_TYPE_JPEG = "JPEG";
const IMAGE_TYPE_JPEG2000 = "JPEG2000";
const IMAGE_TYPE_PNG = "PNG";

const SOCKET_PROTOCOL_DEFAULT = "DEFAULT";
const SOCKET_PROTOCOL_TRACKER_COBAN = "COBAN";
const SOCKET_PROTOCOL_TRACKER_GOOMI = "GOOMI";
const SOCKET_PROTOCOL_TRACKER_Y202 = "Y202";

const NETWORK_TRANSPORT_PROTOCOL_TCP = "TCP";
const NETWORK_TRANSPORT_PROTOCOL_UDP = "UDP";

const HTTP_METHOD_GET = "GET";
const HTTP_METHOD_POST = "POST";

#region Error code
const ERROR_CODE_UNKNOWN = "UNKNOWN";
const ERROR_CODE_ADMINISTRATOR_ONLY = "ADMINISTRATOR_ONLY";
const ERROR_CODE_INVALID = "INVALID";
const ERROR_CODE_NOT_FOUND = "NOT_FOUND";
const ERROR_CODE_REQUIRED_MISSING = "REQUIRED_MISSING";
#endregion Error code

#region Seconds in periods
const SECOND_MINUTE = 60;
const SECOND_MINUTE_5 = SECOND_MINUTE * 5;
const SECOND_MINUTE_10 = SECOND_MINUTE_5 * 2;
const SECOND_MINUTE_15 = SECOND_MINUTE_5 * 3;
const SECOND_MINUTE_30 = SECOND_MINUTE_10 * 3;
const SECOND_HOUR = SECOND_MINUTE_30 * 2;
const SECOND_HOUR_2 = SECOND_HOUR * 2;
const SECOND_HOUR_12 = SECOND_HOUR_2 * 6;
const SECOND_DAY = SECOND_HOUR_12 * 2;
const SECOND_DAY_2 = SECOND_DAY * 2;
const SECOND_DAY_3 = SECOND_DAY * 3;
const SECOND_DAY_12 = SECOND_DAY_2 * 6;
const SECOND_DAY_15 = SECOND_DAY * 15;
const SECOND_WEEK = SECOND_DAY * 7;
const SECOND_WEEK_2 = SECOND_WEEK * 2;
const SECOND_WEEK_4 = SECOND_WEEK_2 * 2;
const SECOND_MONTH = SECOND_DAY_15 * 2;
const SECOND_MONTH_2 = SECOND_MONTH * 2;
const SECOND_MONTH_3 = SECOND_MONTH * 3;
const SECOND_MONTH_6 = SECOND_MONTH_3 * 2;
const SECOND_YEAR = SECOND_MONTH_6 * 2;
#endregion Seconds in periods
?>