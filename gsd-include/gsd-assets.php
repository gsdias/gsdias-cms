<?php

/**
 * @author     Goncalo Silva Dias <mail@gsdias.pt>
 * @copyright  2014-2015 GSDias
 *
 * @link       https://bitbucket.org/gsdias/gsdias-cms/downloads
 * @since      File available since Release 1.0
 */
defined('GVALID') or die;
if (@$site->arg(1) == 'images') {
    $iid = is_numeric(@$site->arg(2)) ? $site->arg(2) : 0;
    $name = explode('.', @$site->arg(2));

    if (sizeof($name) === 2 || $iid) {
        if (sizeof($name) === 2) {
            $mysql->reset()
                ->select('iid, extension')
                ->from('images')
                ->where('name = ? AND deleted IS NULL')
                ->where('AND extension = ?')
                ->values(array($name[0], @$name[1]))
                ->exec();
        } else {
            $mysql->reset()
                ->select('iid, extension')
                ->from('images')
                ->where('iid = ? AND deleted IS NULL')
                ->values($iid)
                ->exec();
        }

        $image = $mysql->singleline();

        $asset = sprintf(ASSETPATH.'images/%d.%s', @$image->iid, @$image->extension);

        if ($mysql->total) {
            $size = getimagesize($asset);

            $fp = fopen($asset, 'rb');
            if ($size && $fp) {
                header("Content-type: {$size['mime']}");
                fpassthru($fp);
                exit;
            }
        } else {
            header('Location: /gsd-image.php', true, 302);
        }
    }
}
if (@$site->arg(1) == 'documents') {
    $iid = is_numeric(@$site->arg(2)) ? $site->arg(2) : 0;
    $name = explode('.', @$site->arg(2));

    if (sizeof($name) === 2 || $iid) {
        if (sizeof($name) === 2) {
            $mysql->reset()
                ->select('did, extension')
                ->from('documents')
                ->where('name = ? AND deleted IS NULL')
                ->where('AND extension = ?')
                ->values(array($name[0], @$name[1]))
                ->exec();
        } else {
            $mysql->reset()
                ->select('name, did, extension')
                ->from('documents')
                ->where('did = ? AND deleted IS NULL')
                ->values($iid)
                ->exec();
        }

        $document = $mysql->singleline();

        $asset = sprintf(ASSETPATH.'documents/%d.%s', @$document->did, @$document->extension);
        $filename = sizeof($name) === 2 ? $site->arg(2) : sprintf('%s.%s', @$document->name, @$document->extension);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $asset);

        if ($mysql->total) {
            header('Content-Description: File Transfer');
            header('Content-Type: '.$mime, true, 200);
            header('Accept-Ranges: bytes');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: '.filesize($asset));
            ob_clean();
            flush();
            readfile($asset);
            exit;
        } else {
            redirect();
        }
    }
}
