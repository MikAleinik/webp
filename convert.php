<?php
$quality = 80;
$ext = "webp";
$dirSeparator = "/";
$fileSeparator = ".";
$imageExt = array("jpg", "jpeg", "png", "gif");
$pathToImages = realpath(__DIR__ . "/iblock");

$counter = 0;

$directory = scandir($pathToImages);
for ($i = 2; $i < count($directory); $i += 1) {
	$path = $pathToImages . $dirSeparator . $directory[$i];
	if (is_dir($path)) {
		$imageFiles = scandir($path);
		for ($j = 2; $j < count($imageFiles); $j += 1) {
			if (is_file($path . $dirSeparator . $imageFiles[$j])) {
				$file = explode($fileSeparator, $imageFiles[$j]);
				if (in_array($file[1], $imageExt) && !in_array($file[0] . $fileSeparator . $ext, $imageFiles)) {
					$convertedFile = $file[0] . $fileSeparator . $ext;
					convertImageToWebp($path . $dirSeparator . $imageFiles[$j], $path . $dirSeparator . $convertedFile, $quality);
					$counter += 1;
					fwrite(STDOUT, "\r" . $counter . " files converted");
				}
			}
		}
	}
}

echo "\r\n[" . date("d.m.Y H:m:s") . "] Total " . $counter . " files converted to " . $ext . "\r\n";

/**
 * @param string $inputFile: relative or absolute path
 * @param string $outputFile: relative or absolute path
 * @param int $quality of output: 0 is worst, 100 is best
 * @return void
 */
function convertImageToWebp(string $inputFile, string $outputFile, int $quality = 100): void
{
	$fileType = exif_imagetype($inputFile);

	switch ($fileType) {
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($inputFile);
			imagepalettetotruecolor($image);
			imagealphablending($image, true);
			imagesavealpha($image, true);
			break;
		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($inputFile);
			break;
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($inputFile);
			imagepalettetotruecolor($image);
			imagealphablending($image, true);
			imagesavealpha($image, true);
			break;
		case IMAGETYPE_WEBP:
			rename($inputFile, $outputFile);
			return;
		default:
			return;
	}

	imagewebp($image, $outputFile, $quality);

	imagedestroy($image);
}
