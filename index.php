<?php

require_once 'vendor/autoload.php';

use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;

$connectionString = "DefaultEndpointsProtocol=https;AccountName=paublobazure;AccountKey=oee3reWNJOevmObuFXCpMiBdLv47oSLjnaue4doa1KW+H1RAEWzQMhjUNYodDFmm5Dursa/djMhuIUct2901qw==;";

$containerName = "paublob";

// Create blob client.
$blobClient = BlobRestProxy::createBlobService($connectionString);

if (isset($_POST['upload'])) {
	$photoFile = strtolower($_FILES["file"]["name"]);
	$content = fopen($_FILES["file"]["tmp_name"], "r");
	$blobClient->createBlockBlob($containerName, $photoFile, $content);
	header("Location: index.php");
}

$listBlobsOptions = new ListBlobsOptions();
$listBlobsOptions->setPrefix("");

$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>

<!DOCTYPE html>
<html>
<head>
	<title>Submission 2 Menjadi Azure Cloud Developer!</title>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
	<div class="container mt-4">

		<h1 class="text-center">Submission 2 Menjadi Azure Cloud Developer!</h1>
		
		<div class="mt-4 mb-2">
			<form class="d-flex justify-content-center" action="index.php" method="post" enctype="multipart/form-data">
				<input type="file" name="file" accept=".jpeg,.jpg,.png" required="">
				<br>
				<input type="submit" name="upload" value="Upload">
			</form>
		</div>

		<h2>Total Files : <?php echo sizeof($result->getBlobs())?></h2>
		<table class='table table-hover'>
			<thead>
				<tr>
					<th>Name File</th>
					<th>URL File</th>
				
				</tr>
			</thead>
			<tbody>
				<?php

				do {
					foreach ($result->getBlobs() as $blob)
					{
						?>
						<tr>
							<td><?php echo $blob->getName() ?></td>
							<td><?php echo $blob->getUrl() ?></td>
							<td>
								<form action="analyze.php" method="post">
									<input type="hidden" name="url" value="<?php echo $blob->getUrl()?>">
									<input type="submit" name="submit" value="Analisa" class="btn btn-success">
								</form>
							</td>
						</tr>
						<?php
					}
					$listBlobsOptions->setContinuationToken($result->getContinuationToken());
				} while($result->getContinuationToken());

				?>
			</tbody>
		</table>

	</div>
</body>
</html>