<?php
/**
 * Created by PhpStorm.
 * User: alexis
 * Date: 10/07/2016
 * Time: 12:47
 */

namespace AppBundle\Mini;


class MiniImage
{

    public function createMini($image, $repMin='mini', $widthMin=200, $heightMin=200, $backGroundColor='blanc')
    {

          $colFond='blanc';

        //creation d'une copie de l'image originale (création ressource)
        // $image = 'images/image_originale.png';
        $ext = pathinfo($image, PATHINFO_EXTENSION );
        // echo 'image_originale '.$image.'<br/>';
        // echo 'ext: '.$ext.'<br/>';
        //si $image est un jpg
        if ($ext=='jpg' || $ext=='jpeg' || $ext=='JPEG' || $ext=='JPG')
        {
            $imgcopy  = imagecreatefromjpeg($image);
            //creation d'une nouvelle image en couleurs vraies
            $imgmini = imagecreatetruecolor($widthMin , $heightMin);
        }
        elseif ($ext=='gif')//si $image est un gif
        {
            $imgcopy = imagecreatefromgif($image);
            //en 256 couleurs
            $imgmini = imagecreate($widthMin , $heightMin);
        }
        elseif ($ext=='png')
        {
            $imgcopy = imagecreatefrompng($image); //si $image est un png
            $imgmini = imagecreatetruecolor($widthMin , $heightMin);
        }
        // echo 'imgmini: '.$imgmini;
        //création d'une couleur avec un code RVB ($r, $v, $b ) pour l'image $imgmini
        //noir
        if ($colFond=='noir')
        {
            $r=0 ;
            $v= 0 ;
            $b=0;
        }
        else //blanc
        {
            $r=255 ;
            $v=255 ;
            $b=255;
        }

        $couleur_fond = imagecolorallocate($imgmini, $r, $v, $b);

        //Le premier appel à imagecolorallocate() créé la couleur et
        //l'utilise pour remplir le fond des images en 256 couleurs créées en utilisant imagecreate().
        $x=0; $y=0; //toute l'image est remplie
        //remplissage avec la couleur de fond, à partir du point de coordonnées x , y  de l'image (le coin supérieur gauche de l'image est l'origine 0,0).
        imagefill($imgmini, $x, $y, $couleur_fond);

        // On peux rendre l'arrière-plan transparent si != jpg
        imagecolortransparent($imgmini,$couleur_fond);


        // "copie" un rectangle de dimensions $largeur_copy, $hauteur_copy , à partir du point de coordonnées $xcopy, $ycopy  de l'image $imgcopy,
        // puis le "colle" avec de nouvelles dimensions $largeur_finale_copy, $hauteur_finale_copy dans $imgmini

        //determination du rapport de reduction
        //dimension de la zone "à copier" de l'image $imgcopy
        // $largeur_copy=400; $hauteur_copy=300; //par ex si on veut une zone de 400px*300px

        $dim = getimagesize($image);//si on veut toute l'image
        // print_r($dim);

        $largeur_copy = $dim[0];
        $hauteur_copy = $dim[1];
        //dimension de cette zone "à coller" dans l'image $imgmini
        $largeur_finale_copy = $widthMin;
        $hauteur_finale_copy = $heightMin;

        //calcul du rapport proportionnel
        if ($largeur_copy > $hauteur_copy)
        {
            $rapport = $largeur_finale_copy / $largeur_copy;
            $hauteur_finale_copy = $rapport * $hauteur_copy;
        }
        else
        {
            $rapport = $hauteur_finale_copy / $hauteur_copy;
            $largeur_finale_copy = $rapport * $largeur_copy;
        }

        // echo "largeur finale : $largeur_finale_copy - hauteur finale $hauteur_finale_copy - rapport : $rapport ";
        //Copie, redimensionne, colle $imgcopy dans $imgmini
        //pour centrer dans la $imgmini
        $xmini=($widthMin - $largeur_finale_copy)/2;
        $ymini=($heightMin - $hauteur_finale_copy)/2;

        $xcopy=0; $ycopy=0;//$xcopy, $ycopy à partir du du coin haut gauche de l'image $imgcopy
        imagecopyresampled($imgmini , $imgcopy, $xmini, $ymini, $xcopy, $ycopy, $largeur_finale_copy, $hauteur_finale_copy, $largeur_copy, $hauteur_copy);

        //////////copyright
        // $couleurcopyright = imagecolorallocatealpha($imgmini,255,255,255,60);
        // $couleurtext = imagecolorallocate($imgmini,0,0,0);

        // imagefilledrectangle($imgmini,$xmini,$ymini,floor($largeur_finale_copy+ $xmini)-2,$ymini+20,$couleurcopyright);

        // $copyright="copyright ac";
        // imagestring($imgmini,3,$xmini+2,$ymini+2,$copyright,$couleurtext);

        // création du fichier jpg, gif ou png  final

        //envoi une image jpg de qualité de 90 %
        $c  = 90;
        //envoi une image jpg de qualité de 80 %
        $c2 = 80;

        // vers un navigateur

        // header('Content-type: image/png');
        // imagepng($imgmini);

        $nom_image = "mini-".basename($image);
        // ou un fichier
        $finalUrl = __DIR__.'/../../../web/'.$repMin.'/'.$nom_image;

        if ($ext=='jpg' || $ext=='jpeg')
        {
            //dans un fichier avec une compression de $c % (entier)
            imagejpeg($imgmini ,$finalUrl , $c);
        }
        elseif ($ext=='gif')
        {
            imagegif($imgmini ,$finalUrl ); //dans un fichier
        }
        elseif ($ext=='png')
        {
            //dans un fichier
            imagepng($imgmini  ,$finalUrl  ); //dans un fichier
        }

        //libération des ressources
        imagedestroy($imgcopy);
        imagedestroy($imgmini);

    }
}