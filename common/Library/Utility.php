<?php

namespace common\Library;

use yii\base\Component;
use Yii;

class Utility extends Component
{
    public function processPath($path)
    {
        $trim = trim($path);
        $dashed = str_replace(' ', '_', $trim);
        $sanitized = str_replace('.', '', $dashed);
        $decommad = str_replace(',', '', $sanitized);
        $sanitized_path = str_replace("'", '', $decommad);
        return strtolower(mb_substr($sanitized_path, 0, 10)) . \Yii::$app->security->generateRandomString(5);
    }

    // Stores identity object in a session variable

    public function PersistIdentity()
    {
        if (!Yii::$app->user->isGuest && property_exists(Yii::$app->user->identity, 'Key')) {
            //Yii::$app->recruitment->printrr(Yii::$app->user->identity);
            Yii::$app->session->set('user', Yii::$app->user->identity);
        }
    }

    public function absoluteUrl()
    {
        return \yii\helpers\Url::home(true);
    }

    public function webroot()
    {
        return \Yii::getAlias('@web');
    }


    public function printrr($var)
    {
        print '<pre>';
        print_r($var);
        print '<br>';
        exit('turus!!!');
    }

    function currentCtrl($ctrl)
    {
        $controller = Yii::$app->controller->id;

        if (is_array($ctrl) && in_array($controller, $ctrl)) {
            return true;
        } else if ($controller == $ctrl) {
            return true;
        } else {
            return false;
        }
    }

    public function currentaction($ctrl, $actn)
    { //modify it to accept an array of controllers as an argument--> later please
        $controller = Yii::$app->controller->id;
        $action = Yii::$app->controller->action->id;

        if ($controller == $ctrl && is_array($actn) && in_array($action, $actn)) {
            return true;
        } else if (is_array($ctrl) && in_array($controller, $ctrl)) {
            return true;
        } else if ($controller == $ctrl && $action == $actn) {
            return true;
        } else {
            return false;
        }
    }

    public function DownloadFile($base64String, $fileName)
    {
        // Decode the base64 string to binary data
        $fileData = base64_decode($base64String);

        // Generate a unique temporary file path
        $tempFilePath = Yii::$app->runtimePath . '/' . uniqid() . '_' . $fileName;

        // Save the binary data to the temporary file
        file_put_contents($tempFilePath, $fileData);

        // Set the response headers for file download
        Yii::$app->response->sendFile($tempFilePath, $fileName, [
            'mimeType' => 'application/octet-stream',
            'inline' => false,
        ]);

        // Delete the temporary file
        unlink($tempFilePath);
    }

    //Log function

    public function log($message, $name = null)
    {
        $message = print_r($message, true);
        if ($name) {
            $filename = 'log/' . $name . '.log';
        } else {
            $filename = 'log/signature.log';
        }
        $req_dump = print_r($message, TRUE);
        $fp = fopen($filename, 'a');
        fwrite($fp, $req_dump);
        fclose($fp);
    }

    public function logResult($message, $name = null)
    {
        $message = print_r($message, true);

        if ($name) {
            $filename = 'log/' . $name . '.log';
        } else {
            $filename = 'log/result.log';
        }
        $req_dump = print_r($message, TRUE);
        $fp = fopen($filename, 'a');
        fwrite($fp, $req_dump);
        fclose($fp);
    }

    public function DocumentHasSignature($service, $DocumentNumber, $column = 'Signed_Document')
    {
        $type = Yii::$app->session->get('Document');
        $filter = [];
        if ($type == 'Salary_Voucher' || $type == 'itc') {
            $filter = [
                'No' => $DocumentNumber ?: Yii::$app->session->get('metadata')['Application']
            ];
        } else { // other doc like procurement related: // opinion, analysis etc...
            $filter = [
                'Code' => $DocumentNumber ?? Yii::$app->session->get('metadata')['Application']
            ];
        }

        $document = Yii::$app->navhelper->findOne($service, $filter);
        if (is_object($document) && property_exists($document, $column)) {
            return $document->$column;
        }
        return false;
    }

    public function read($list, $title, $link)
    {
        if ($this->validateSharepoint($link)) {
            if ($link) {
                $parts = explode("/", $link);
                $list = $parts[6];
            }
            $result = Yii::$app->recruitment->Read($list, $title);
            //returns base64 from sharepoint integration component
            return is_string($result) ? $result : false;
        }
    }

    public function readLibrary($link)
    {
        if ($this->validateSharepoint($link)) {
            list($scheme, $relativePath) = explode('sites', $link);
            $relativeUrl = '/sites' . $relativePath;
            $res = Yii::$app->sharepoint->readLibrary($relativeUrl);
            $result = base64_encode($res);
            return $result;
        }
    }

    public function validateSharepoint($link)
    {
        if (strpos($link, 'sharepoint.com') === false) {
            throw new \yii\web\BadRequestHttpException('Invalid SharePoint link (' . $link . '). Re-attach Properly or Seek guidance from ICT Support.');
        }
        return true;
    }

    public function isValidSharepointLink($link)
    {
        if (strpos($link, 'sharepoint.com') === false) {
            return false;
        }
        return true;
    }

    public function isAttachment($link)
    {
        if (strpos($link, 'Attachments') === false) {
            return false;
        }
        return true;
    }

    public function getMimetype($base64String)
    {
        $binary = base64_decode($base64String, true);
        if ($binary === false) {
            return null;
        }

        // create finfo resource
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->buffer($binary);
    }

    public function refactorArray($arr, $from, $to, $extraFields = [])
    {
        $list = [];

        if (is_array($arr)) {

            foreach ($arr as $item) {
                // Check Valid fields to Add to the "to" values
                $valid = [];
                if (count($extraFields) > 0) {
                    foreach ($extraFields as $exfield) {
                        if (property_exists($item, $exfield)) {
                            $valid[] = $item->$exfield;
                        }
                    }
                }

                $ValuesToAppend = '';
                if (count($valid)) {
                    $ValuesToAppend = implode(' - ', $valid);
                }
                if (property_exists($item, $from) && property_exists($item, $to)) {

                    $list[] = [
                        'Code' => $item->$from,
                        'Desc' => strlen($ValuesToAppend) ? $item->$to . ' - ' . $ValuesToAppend : $item->$to
                    ];
                }
            }
            return yii\helpers\ArrayHelper::map($list, 'Code', 'Desc');
        }

        return $list;
    }

    public function dropDown(array $data, $to, $from, $extraFields): array
    {
        $dd = $this->refactorArray($data, $from, $to, $extraFields);
        if (is_array($dd)) {
            krsort($dd);
        }
        return $dd;
    }

    // Compliance Description from Average Status

    public static function getDescriptiveStatusFromConfig(float $averageStatus): string
    {
        $thresholds = Yii::$app->params['app.statusThresholds'];

        foreach ($thresholds as $description => $range) {
            if ($averageStatus >= $range['min'] && $averageStatus <= $range['max']) {
                return $description;
            }
        }
        return 'Undefined Status'; // Handle cases outside your defined ranges
    }


    // Badge background based on average status
    public static function getBadgeFromConfig(float $averageStatus): string
    {
        $thresholds = Yii::$app->params['app.statusClasses'];

        foreach ($thresholds as $class => $range) {
            if ($averageStatus >= $range['min'] && $averageStatus <= $range['max']) {
                return $class;
            }
        }
        return 'bg-secondary'; // Handle cases outside your defined ranges
    }

    // pseudo clases for non-background representations like text
    public static function getPseudoClassFromConfig(float $averageStatus): string
    {
        $thresholds = Yii::$app->params['app.classes'];

        foreach ($thresholds as $class => $range) {
            if ($averageStatus >= $range['min'] && $averageStatus <= $range['max']) {
                return $class;
            }
        }
        return 'secondary'; // Handle cases outside your defined ranges
    }
}
