<?php

namespace App\Http\Controllers;

use App\Models\ExampleModel;
use Illuminate\Http\Request;


class DBController extends Controller{
    public function getFirstExample(){
        return ExampleModel::first()->toArray(); //Ottiene il primo elemento della tabella a cui è collegato ExampleModel
    }

    public function getAllExamples(){
        $examples = ExampleModel::all(); //Ottiene tutti gli elementi nella tabella a cui è collegato ExampleModel
        return $examples;
    }
    public function getAllIDs(){
        $examples = ExampleModel::all()->pluck('id'); //Ottiene tutti gli elementi nella tabella a cui è collegato ExampleModel
        return $examples;
    }

    public function insertOneExample(Request $request){
        try{
            $validatedData = ExampleModel::validate($request); //Controllo che la richiesta soddisfi tutti i campi di validazione del modello
            ExampleModel::create($validatedData); //Inserisco i dati nel db dopo che sono stati validati

        }catch(\Exception $e){
            //response()->json(['error' => $e->getMessage()]);
            return redirect()->route("homepage")->with('alert-type','error')->with('message','Errore durante il caricamento della postazione');
        }
        return redirect()->route("homepage")->with('alert-type','success')->with('message','Postazione aggiunta correttamente');
    }
    public function updateSettings(Request $request){ //Funzione per aggiornare uno o piu valore di un elemento nella tabella del db collegata al modello
        try{
            
            $id = $request->input('id'); //Ottengo l'id che voglio cercare dalla request
            $record = ExampleModel::where('id', $id)->first()->toArray(); //First ferma la ricerca dopo aver trovato la prima occorrenza
            $validatedData = ExampleModel::validate($request);//Controllo che la richiesta soddisfi tutti i campi di validazione del modello
            //$record = ExampleModel::find($id); Questa funzione può essere usata per fare la ricerca di una chiave primaria
            if ($record) {
                $record->update($validatedData); //Effettua l'operazione di aggiornamento sul db
                $record->save(); //Salvo i cambiamenti
            }
        }catch(\Exception $e){
            return redirect()->route("homepage")->with('alert-type','error')->with('message','Errore durante l\'aggiornamento dei settings');
        }
        return redirect()->route("homepage")->with('alert-type','success')->with('message','Settings aggiornati correttamente');

    }
}
