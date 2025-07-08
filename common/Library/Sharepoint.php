<?php

/**
 * Created by PhpStorm.
 * User: HP ELITEBOOK 840 G5
 * Date: 3/10/2020
 * Time: 2:27 PM
 */



namespace common\library;

use yii;
use yii\base\Component;
use common\models\Hruser;
use Office365\Runtime\Auth\NetworkCredentialContext;
use Office365\SharePoint\ClientContext;
use Office365\Runtime\Auth\UserCredentials;
use Office365\Runtime\Auth\AuthenticationContext;
use Office365\Runtime\Auth\ClientCredential;
use Office365\Runtime\Utilities\RequestOptions;
use Office365\Runtime\ClientRuntimeContext;
use Office365\Runtime\Http\RequestException as HttpRequestException;
use Office365\SharePoint\AttachmentCreationInformation;
use Office365\SharePoint\CamlQuery;
use Office365\SharePoint\FieldText;
use Office365\SharePoint\FieldUserValue;
use Office365\SharePoint\ListCreationInformation;
use Office365\SharePoint\SPList;
use Office365\SharePoint\Web;
use Office365\SharePoint\ListTemplateType;
use Office365\SharePoint\FileCreationInformation;
use Office365\SharePoint\File;
use Office365\SharePoint\ListItemCreationInformation;
use stdClass;
use yii\helpers\BaseUrl;

class Sharepoint extends Component
{
    //SHAREPOINT UPLOAD

    public function sharepoint_attach($filepath)
    {  //read list

        $targetLibraryTitle = env('SP_LIBRARY');

        try {

            $ctx = $this->connectWithUserCredentials(Yii::$app->params['sharepointUrl'] . '/' . env('SP_SITE'), env('SP_USERNAME'), env('SP_PASSWORD'));
            $site = $ctx->getSite();
            // GET USER
            $user = $ctx->getWeb()->getSiteUsers()->getByEmail(env('SP_USERNAME'))->get()->executeQuery();
            // Get List
            $list = $ctx->getWeb()->getLists()->getByTitle(env('SP_LIST'));

            //create list item
            $metadata = Yii::$app->session->get('metadata');
            $taskProps = array(
                'Title' => basename($filepath),
                'Desc' => $metadata['Leavetype'],
                'Docno' => $metadata['Application'],
                'Employee' => $metadata['Employee']
            );
            $listItem = $list->addItem($taskProps);
            //add attachment
            $localPath = $filepath;
            $listItem->getAttachmentFiles()->add($localPath);
            //update list item system metadata
            $fieldValues = array(
                'Editor' => FieldUserValue::fromUser($user),
                'Author' => FieldUserValue::fromUser($user),
            );
            $listItem->validateUpdateListItem($fieldValues)->executeQuery();


            return $this->searchFile($list, env('SP_LIST'), basename($filepath));
        } catch (\Exception $e) {
            print 'Exception : ' . $e->getMessage() . "\n";
        }
    }

    // Attach Binary

    public function attach_binary($binary, $list, $fileName, $setmetadata = [])
    {

        try {

            $ctx = $this->connectWithUserCredentials(Yii::$app->params['sharepointUrl'] . '/' . env('SP_SITE'), env('SP_USERNAME'), env('SP_PASSWORD'));
            $site = $ctx->getSite();
            // GET USER
            $user = $ctx->getWeb()->getSiteUsers()->getByEmail(env('SP_USERNAME'))->get()->executeQuery();
            // Get List
            $listCtx = $ctx->getWeb()->getLists()->getByTitle($list);

            //list entry metadata
            $metadata = Yii::$app->session->get('metadata');
            $taskProps = count($setmetadata) > 0 ? $setmetadata : array(
                'Title' => $fileName,
                'Desc' => $metadata['Leavetype'],
                'Docno' => $metadata['Application'],
                'Employee' => $metadata['Employee']
            );
            // create new list item
            //$itemCreationInfo = new ListItemCreationInformation();
            $listItem = $listCtx->addItem($taskProps);

            //Attach file

            $attachmentInfo = new AttachmentCreationInformation();
            $attachmentInfo->FileName = $fileName;
            $attachmentInfo->ContentStream = $binary;
            $listItem->getAttachmentFiles()->add($attachmentInfo);

            $fieldValues = array(
                'Editor' => FieldUserValue::fromUser($user),
                'Author' => FieldUserValue::fromUser($user),
            );
            $listItem->validateUpdateListItem($fieldValues)->executeQuery();
            //return json_encode($fieldValues);
            return $this->searchFile($listCtx, $list, $taskProps['Title']); // Be mindlful of caml query usage of Title value
        } catch (\Exception $e) {
            print 'Exception : ' . $e->getMessage() . "\n";
        }
    }

    public function attach_toLibrary($library, $binary, $filename, $metadata)
    {
        try {
            $ctx = $this->connectWithUserCredentials(Yii::$app->params['sharepointUrl'] . '/' . env('SP_SITE'), env('SP_USERNAME'), env('SP_PASSWORD'));

            // Set the binary content directly instead of using a file path
            $binaryContent = $binary; // Assuming file content is received through POST request

            $libraryTitle = $library;
            $lib = $ctx->getWeb()->getLists()->getByTitle($libraryTitle);

            // Upload the file using binary content
            $uploadFile = $lib->getRootFolder()->uploadFile($filename, $binaryContent)->executeQuery();

            $listItem = $uploadFile->getListItemAllFields();
            $listItem->setProperty("Title", $metadata->description)->update()->executeQuery(); // Update file metadata
            $listItem->setProperty("Issueid", $metadata->id)->update()->executeQuery(); // Update file metadata
            $listItem->setProperty("Issue", $metadata->description)->update()->executeQuery(); // Update file metadata

            //print "File {$uploadFile->getServerRelativeUrl()} has been uploaded\r\n";
            return env('SP_ROOT') . $uploadFile->getServerRelativeUrl();
        } catch (\Exception $e) {
            return "An error occurred: " . $e->getMessage();
        }
    }

    public function searchFile(SPList $list, $listTitle, $documentTitle)
    {
        $query = new CamlQuery();
        $query->ViewXml = "<View><Query><Where><Eq><FieldRef Name='Title' /><Value Type='Text'>$documentTitle</Value></Eq></Where></Query></View>";
        $listItemCollection = $list->getItems($query)->executeQuery()->toJson();
        // search you attachment from the collection
        $key = array_search($documentTitle, array_column($listItemCollection, 'Title'));
        $actualDocument = $listItemCollection[$key];
        return $fileUrl = env('SP_URL') . "/" . env('SP_SITE') . "/Lists/" . $listTitle . "/Attachments/" . $actualDocument['Id'] . "/" . $actualDocument['Title'];
    }

    // Read Document from a List

    public function Read($listTitle, $documentTitle)
    {
        $ctx = $this->connectWithUserCredentials(Yii::$app->params['sharepointUrl'] . '/' . env('SP_SITE'), env('SP_USERNAME'), env('SP_PASSWORD'));
        $list = $ctx->getWeb()->getLists()->getByTitle($listTitle);

        $query = new CamlQuery();
        $query->ViewXml = "<View><Query><Where><Eq><FieldRef Name='Title' /><Value Type='Text'>$documentTitle</Value></Eq></Where></Query></View>";

        $listItemCollection = $list->getItems($query)->executeQuery()->toJson();
        // Extract titles into an array for easier searching
        $titles = array_column($listItemCollection, 'Title');
        // Search through entire titles array in context of passed document title -  returns array index where title is found
        $key = array_search($documentTitle, $titles);
        // get array value of returned index
        $actualDocument = $listItemCollection[$key];

        if (count($listItemCollection) && is_array($actualDocument)) {

            $fileUrl = "/sites/" . env('SP_SITE') . "/Lists/" . $listTitle . "/Attachments/" . $actualDocument['Id'] . "/" . $actualDocument['Title'];
            $fileContent = \Office365\sharePoint\File::openBinary($ctx, $fileUrl);
            return base64_encode($fileContent);
        }

        return [
            'key' => $key,
            'attachment' => $actualDocument,
            'collection' => $listItemCollection
        ];
    }


    //CREATING LISTS

    public function ListCreate($listTitle)
    {
        $ctx = $this->connectWithUserCredentials(Yii::$app->params['sharepointUrl'] . '/' . env('SP_SITE'), env('SP_USERNAME'), env('SP_PASSWORD'));

        $listTitle = $listTitle;
        $info = new ListCreationInformation($listTitle);
        $info->BaseTemplate = ListTemplateType::DocumentLibrary;
        $list = $ctx->getWeb()->getLists()->add($info)->executeQuery();
        return $list;
    }



    /**
     * @param \Office365\PHP\Client\SharePoint\SPList $list
     */
    public static function deleteList(SPList $list)
    {
        $ctx = $list->getContext();
        $list->deleteObject();
        $ctx->executeQuery();
    }

    //Read a file from a library path

    function readLibrary($targetPath)
    {
        $ctx = $this->connectWithUserCredentials(Yii::$app->params['sharepointUrl'] . '/' . env('SP_SITE'), env('SP_USERNAME'), env('SP_PASSWORD'));
        $fileContent = File::openBinary($ctx, $targetPath, false);
        return $fileContent;
    }



    /*Sharepoint Authentication Context methods */


    function connectWithUserCredentials($url, $username, $password)
    {
        $authCtx = new AuthenticationContext($url);
        $authCtx->acquireTokenForUser($username, $password);
        $ctx = new ClientContext($url, $authCtx);
        return $ctx;
    }

    function connectWithNTLMAuth($url, $username, $password)
    {
        $authCtx = new NetworkCredentialContext($username, $password);
        $authCtx->AuthType = CURLAUTH_NTLM;
        $ctx = new ClientContext($url, $authCtx);
        return $ctx;
    }

    function connectWithAppOnlyToken($url, $clientId, $clientSecret)
    {
        $authCtx = new AuthenticationContext($url);
        $authCtx->acquireAppOnlyAccessToken($clientId, $clientSecret);
        $ctx = new ClientContext($url, $authCtx);
        return $ctx;
    }
}
