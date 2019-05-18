<?php
    require_once 'vendor/autoload.php';
    require_once "./random_string.php";
    use MicrosoftAzure\Storage\Blob\BlobRestProxy;
    use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
    use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
    use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
    use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
    $connectionString = "DefaultEndpointsProtocol=https;AccountName=kevinwebapp;AccountKey=ur8LcUUCbykQbyPlUb7L2vcBB64MlHMZVSNKDFc/6FUUcWrOlKRaZRaTSAzx0GrMdcMCgB5vgA8nOKudVMUCPw==";
    // Create blob client.
    $blobClient = BlobRestProxy::createBlobService($connectionString);
    $createContainerOptions = new CreateContainerOptions();
    $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
    // Set container metadata.
    $createContainerOptions->addMetaData("key1", "value1");
    $createContainerOptions->addMetaData("key2", "value2");
    $containerName = "blockblobs".generateRandomString();
    try{
        $blobClient->createContainer($containerName, $createContainerOptions);
        $fileToUpload = 'asd'.generateRandomString().$_FILES['fileToUpload']['name'];
        //echo $fileToUpload;
        $content = fopen($_FILES['fileToUpload']['tmp_name'].'', "r");
        $blobClient->createBlockBlob($containerName,$fileToUpload,$content);
        
    }catch(ServiceException $e){
        
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Analyze Sample</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.8.10/themes/smoothness/jquery-ui.css" type="text/css">
    <script type="text/javascript" src="//ajax.aspnetcdn.com/ajax/jquery.ui/1.8.10/jquery-ui.min.js"></script>
    
</head>
<body>
 
<script type="text/javascript">
    function processImage() {
        var subscriptionKey = "53aafa0f1e5a4f2cadee68a2b1253fec";
        var uriBase =
            "https://centralus.api.cognitive.microsoft.com/";
        var params = {
            "visualFeatures": "Categories,Description,Color",
            "details": "",
            "language": "en",
        };
        var sourceImageUrl = document.getElementById("inputImage").value;
        document.querySelector("#sourceImage").src = sourceImageUrl;
        $.ajax({
            url: uriBase + "?" + $.param(params),
            beforeSend: function(xhrObj){
                xhrObj.setRequestHeader("Content-Type","application/json");
                xhrObj.setRequestHeader(
                    "Ocp-Apim-Subscription-Key", subscriptionKey);
            },
 
            type: "POST",
            data: '{"url": ' + '"' + sourceImageUrl + '"}',
        })
 
        .done(function(data) {
            $("#responseTextArea").val(JSON.stringify(data, null, 2));
        })
 
        .fail(function(jqXHR, textStatus, errorThrown) {
            var errorString = (errorThrown === "") ? "Error. " :
                errorThrown + " (" + jqXHR.status + "): ";
            errorString += (jqXHR.responseText === "") ? "" :
                jQuery.parseJSON(jqXHR.responseText).message;
            alert(errorString);
        });
    };
</script>
 
<h1>Analyze image:</h1>
Enter the URL to an image, then click the <strong>Analyze image</strong> button.
<br><br>
Image to analyze:
<input type="text" name="inputImage" id="inputImage"
    value=<?php echo "https://kevinwebapp.blob.core.windows.net/".$containerName."/".$fileToUpload; ?> />
<button onclick="processImage()">Analyze image</button>
<br><br>
<div id="wrapper" style="width:1020px; display:table;">
    <div id="jsonOutput" style="width:600px; display:table-cell;">
        Response:
        <br><br>
        <textarea id="responseTextArea" class="UIInput"
                  style="width:580px; height:400px;"></textarea>
    </div>
    <div id="imageDiv" style="width:420px; display:table-cell;">
        Source image:
        <br><br>
        <img id="sourceImage" width="400" />
    </div>
</div>
</body>
</html>
