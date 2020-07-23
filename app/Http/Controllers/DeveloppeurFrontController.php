<?php

namespace App\Http\Controllers;
use App\Postuler_offre;
use App\Offre;
use App\developpeurs;
use App\message;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DeveloppeurFrontController extends Controller
{
  /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */
  public function index()
  {
    $devid = auth::user()->developpeur_id;
    $OffresPostuler = Postuler_offre::all()->where('developpeur_id',$devid);
    return view("front.developpeur.demande")->with("OffresPostuler",$OffresPostuler);
  }

  /**
  * Display the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function show($id)
  {
    $dev_id = auth::user()->developpeur_id;
    $lesoffresuser = Postuler_offre::all()->where('developpeur_id',$dev_id);
    $test = false;
    foreach ($lesoffresuser as $offre) {
      if($id == $offre->offre_id){
        $test = true;
      }
    }

    $offre = Offre::Find($id);
    if($offre->vue ){
      $val = $offre->vue;
      $offre->vue = $val + 1 ;
    }else{
      $offre->vue = 1;
    }
    $offre->save();
    return view("front.show")->with("offre",$offre)
    ->with('test', $test);

  }


  /**
  * Show the form for editing the specified resource.
  *
  * @param  int  $id
  * @return \Illuminate\Http\Response
  */
  public function postuler($id)
  {
    $dev_id = auth::user()->developpeur_id;
    $lesoffresuser = Postuler_offre::all()->where('developpeur_id',$dev_id);
    foreach ($lesoffresuser as $offre) {
      if($id == $offre->offre_id){
        return redirect()->route("offre_entreprise.index");
      }
    }
    $offre = Offre::Find($id);
    $emetteur_id = auth::user()->id;
    $destinataire = User::where('entreprise_id',  $offre->entreprise->id)->get();
    $destinataire_id = $destinataire[0]->id;
    $message = new Message;
    $message->emetteur_id = $emetteur_id;
    $message->destinatair_id = $destinataire_id;
    $message->objet = "Nouveau Candidat a votre offre '" . $offre->titre . "'";
    $message->body = "gnagnagnagna" ;
    $message->pj = "gnol";
    $message->save();


    $postuler = new Postuler_offre;
    $postuler->offre_id = $id;
    $postuler->developpeur_id = $dev_id;
    $postuler->type_contrat = "1";
    $postuler->save();
    return redirect()->route("offre_entreprise.index");
  }

}
