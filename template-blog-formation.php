<?php

/**
 * Template Name: Blog (5 columns)
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package WordPress
 * @subpackage Howes
 * @since Howes 1.0
 * @version 1.0
 */


get_header();

//connexion avec la base de donnée
global $wpdb;
?>


<!--les fonctions : récuperation des résultats------------------------------------------------------------------------>
<?php
//fonction pour obtenir le résultat du type de d'école (Ingénieurs, Architecture, ...)
function get_type_diplome($id, $wpdb){
  $resultat = $wpdb->get_results("SELECT nom FROM {$wpdb->prefix}listeformations_type WHERE id=$id");
  return $resultat[0]->nom;
}

//fonction pour obtenir le résultat du nom de l'école (ECAM, Exia, ...)
function get_nom_ecole($id, $wpdb){
  $resultat = $wpdb->get_results("SELECT nom_ecole FROM {$wpdb->prefix}listeformations_ecole WHERE id_ecole=$id");
  return $resultat[0]->nom_ecole;
}

//fonction pour obtenir le résultat du type de formation (Cycle ingénieur généraliste, Cycle ingénieur informatique, ...)
function get_nom_formation($id, $wpdb){
  $resultat = $wpdb->get_results("SELECT nom_formation FROM {$wpdb->prefix}listeformations_nom_formation WHERE id_formation=$id");
  return $resultat[0]->nom_formation;
}

//fonction pour obtenir le niveau d'admission des différentes les-formations
/*function get_niveau_admission($id, $wpdb){
  $resultat = $wpdb->get_results("SELECT type_admission FROM {$wpdb->prefix}listeformations_admission WHERE id_admission=$id");
  return $resultat[0]->type_admission;
}*/

//fonction pour obtenir le résultat du type de rythme de la formation (Classique, Alternance)
function get_rythme_formation($id, $wpdb){
  $resultat = $wpdb->get_results("SELECT type_rythme FROM {$wpdb->prefix}listeformations_rythme WHERE id=$id");
  return $resultat[0]->type_rythme;
}
/*--------------------------------les fonctions : récuperation des résultats-----------------------------------------*/
?>



<!--Feuille de style-------------------------------------------------------->
<style type="text/css">
    .multicriteres {
      border: 3px solid #333;
      margin-left: 35px;
      font-size: 15px;
      width: 1130px;
      height: 230px;
      padding-left: 10px;
      padding-right: 10px;
      padding-bottom: 10px;
      background-color: #C0C0C0;
    }
    form {
      display: inline;
    }
    .multicriteres h1 {
      font-size: 20px;
      margin-top: 8px;
    }
    .multicriteres h2 {
      font-size: 18px;
      margin-top: 14px;
    }
    .multicriteres button {
      margin-top: 18px;
    }
    .lesformations {
      margin-top: 100px;
      margin-bottom: 40px;
      margin-left: 35px;
    }
    .lesformations h1{
      font-size: 30px;
    }
    #Bloc{
      float: left; width: 25%; margin: 0px;

    }
    #Bloc1{
      margin-top: 30px;
    }
    .research{
      float:none;
    }
    .reinitialiser {
      margin-top: 0px;
    }
    @media screen and (max-width:1130px) {
      .box{
        width: 90%;
        margin: 10px auto;
      }
    }
    </style>
<!--/Feuille de style/-------------------------------------------------------->





<!-- Création du formulaire de recherche avec les différentes critères à prendre en compte -->
<div class="box">
  <div class="multicriteres">
    <h1>Affiner la liste en cochant des critères</h1>
      <form action="/les-formations" method="post" id="searchform">
        <div id="Bloc">
          <h2>Type de diplôme</h2>
            <?php
            $resultat = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeformations_type");
            foreach ($resultat as $post){
                echo  '<input type="checkbox" id="Type" name="diplome[]" value="'.$post->id.'"> '.$post->nom.'</br>';
            }
            ?>
        </div>
        <div id="Bloc">
          <h2>Rythme de la formation</h2>
            <?php
            $results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeformations_rythme");
            foreach ($results as $post){
                echo '<input type="checkbox" id="Rythme" name="rythme[]" value="'.$post->id.'"> '.$post->type_rythme.'</br>';
            }
            ?>
        </div>
        <div id="Bloc">
          <h2>Modalité d'admission</h2>
            <?php
            $result = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeformations_modalite_admission");
            foreach ($result as $posts){
                echo  '<input type="checkbox" id="Admission" name="admission[]" value="'.$posts->id.'"> '.$posts->admission.'</br>';
            }
            ?>
        </div>
        <div id="Bloc">
          <h2>Thématique de la formation</h2>
             <select style="font-size:15px;margin-bottom:8px;margin-right:60px;" name="theme" size="1">
                 <?php
                 $re = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}listeformations_domaine");
                 echo '<option value="" selected>--Choisir une thématique--</option>';
                 foreach ($re as $pos){
                     echo '  <option value="'.$pos->id.'"> '.$pos->thematique.'</option>';
                 }
                 ?>
              </select>
        </div>
        <div id="Bloc1">
        <input id="submits" type="submit" style="font-size:12px;" name="btnrechercher" value="Rechercher">
        </div>
      </form>
  </div>
</div>
<!-------------------------------------------------------------------------->



<?php
/*---------------------------------------------------------------------------*/
if(!empty($_POST)){
    //on fait une selection sur toute la table diplome qui est la table principale dans la bdd
    $query = "SELECT * FROM {$wpdb->prefix}listeformations_diplome WHERE 1 ";


  /*---------on rajoute des conditions sur notre requete précédentes en fonction de---------*/
    //si l'utilisateur coche une valeur pour le type d'école
    if(!empty($_POST['diplome'])){
        $query.="AND (";
        foreach ($_POST['diplome'] as $key=>$value) {
            if($key==0){
                $query.= 'type_ecole = '.$value;
            }else{
                $query.=" OR type_ecole = $value ";
            }
        }
        $query.=")";
    }
    //si l'utilisateur coche une valeur pour le rythme de la formation
    if(!empty($_POST['rythme'])){
      $query.=" AND (";
      foreach ($_POST['rythme'] as $key=>$value) {
          if($key==0){
              $query.= 'rythme = '.$value;
          }else{
              $query.=" OR rythme = $value ";
          }
      }
      $query.=")";
    }
    //si l'utilisateur coche une valeur pour la modalité d'admission
    if (!empty($_POST['admission'])) {
      $query.="IN (SELECT id FROM wp_listeformation_diplome_admission WHERE ";
      foreach ($_POST['admission'] as $key => $value) {
        if ($key==0) {
          $query.= 'id_admission = '.$value;
        }else {
          $query.=" OR id_admission = $value ";
        }
      }
      $query.=")";
    }

/*
    if(!empty($_POST['admission'])){
      $query.=" AND (";
      foreach ($_POST['admission'] as $key=>$value) {
          if($key==0){
              $query.= 'admission1 = '.$value;
          }else{
              $query.=" OR admission1 = $value";
          }
      }
      $query.=")";
    } */

    //si l'utilisateur sélectionne une valeur pour la thématique de la formation
    if (!empty($_POST['theme'])){
      $query.=' AND (domaine_formation = '.$_POST['theme'].')';
    }
  /*---------on rajoute des conditions sur notre requete précédentes en fonction de---------*/


    //classé par ordre croissant des écoles
    $query.=" ORDER BY ecole";

    echo $query;

    //récupération du résultat de la requête dans la variable $resultat
    $resultat = $wpdb->get_results($query); ?> </br>


   <!-- création d'un tableau qui affiche les informations des formations -->
   <table align="center" style="border-collapse: collapse; padding-top:100px;">
    <tr>
      <th style="border: 1px solid black; text-align: center;"> Ecole</th>
      <th style="border: 1px solid black; text-align: center;"> Nom de la formation</th>
      <th style="border: 1px solid black; text-align: center;"> Rythme de la formation</th>
      <th style="border: 1px solid black; text-align: center;"> Type d'Ecole</th>
      <th style="border: 1px solid black; text-align: center;"> Admission</th>
    </tr>
<?php
    //si les formations correspondant à la recherche existent, les afficher dans le tableau
    if(!empty($resultat)){
        foreach ($resultat as $row) {
            echo "<tr>";
            echo '<td style="border: 1px solid black; text-align: center; font-weight: bold;">'.get_nom_ecole($row->ecole, $wpdb).'</td>';
            echo '<td style="border: 1px solid black; text-align: center;">'.get_nom_formation($row->formation, $wpdb).'</td>';
            echo '<td style="border: 1px solid black; text-align: center;">'.get_rythme_formation($row->rythme, $wpdb).'</td>';
            echo '<td style="border: 1px solid black; text-align: center;">'.get_type_diplome($row->type_ecole, $wpdb).'</td>';
            echo "</tr>";
        }
        echo "</table>";
    //sinon afficher un message d'erreur afin que l'utilisateur effectue une nouvelle recherche
    }else{
      echo '<h3 style="margin-left: 30px;">Ce type de formation n\'existe pas. Veuillez soumettre une nouvelle recherche.</h3>';
    }
}
?>
<?php get_footer();
