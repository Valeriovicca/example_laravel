<?php 
namespace App\Http\Controllers;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class FileController extends Controller
{//Solitamente le funzioni private sono funzioni di utilità che si usano solo all'interno dello stesso file in cui vengono definite (quindi all'interno della classe)
    public function createExample():View//Funzione per creare la vista
    { 
        $variable = ["a","b","c"];
        return view('nomeView',["nomeVariabile"=>$variable]); //Collego questo controller al file della vista chiamato nomeView e passo a quel file $variable,
                                                              // e nel file mi riferisco ad essa usando nomeVariabile
    }
    public function createAllFiles(){
        $user = Auth::user(); //Ottengo i dati dell'utente attualmente loggato
        $path = 'app/public/uploads/'. $user->city; //Per salvare file all'interno di una cartella con nome della città
        $frames = $this->scanDirectory($path); //Ottengo l'array contenente tutti i file nella directory e sottodirectory passata
        return view('all_files', ['all_frames'=>$frames]); //Restituisco la vista passandole la lista dei files in una variabile chiamata all_files
    }
    private function scanDirectory($directory) //Metodo che scansione tutte le cartelle e sottocartelle a partire da $directory per ottenere
                                                      //in un array di questa forma array["nomeDirectory"] = "nomefile" per ogni file
    {//La funzione è ricorsiva e ogni volta richiama se stessa con il nome di una delle sottodirectory
        $all_files = []; //Array che conterrà tutti i file 
        $directories = Storage::disk('public')->directories($directory); //Ottengo la lista delle cartelle contenute in directory
        $files = Storage::disk('public')->files($directory); //Ottengo la lista dei files contenuti in directory

        if (count($files) !== 0 ){  //Se non ci sono più file termina la funzione
            return $files;

        }else{
            foreach ($directories as $dir) { //Per ogni sottodirectory richiamo la funzione
                $all_files[$dir] = $this->scanDirectory($dir); //Ogni file nella sottodirectory viene messa nell' array con posizione il nome della directory
           }
        }
        return $all_files;
    }
    public function deleteFile(Request $request){ //Funzione per eliminare file dal server
        if ($request) {
            try{
            // Gestisci l'eliminazione delle immagini
                $selected_field = $request->input('toDeleteList'); //Ottengo dal form della view il campo chiamato toDeleteList
                foreach ($selected_field as $path) { //funzione per scorrere ogni elemento che è stato selezionato per essere eliminato
                    // Converti l'URL in un percorso relativo al file storage 
                    if (Storage::disk('public')->exists($path)) { //Verifico se esiste l'elemento passato tramite path
                        Storage::disk('public')->delete($path); //Se esiste lo elimino
                    }
                }
                //return response()->json(['success' => true]); Se non hai implementato ancora i toast usa questo
                return redirect()->back()->with('alert-type','success')->with('message','Immagini eliminate con successo!');
            }catch(\Exception $e){
                //return response()->json(["failed"=>true]);
                return redirect()->route("homepage")->with('alert-type','error')->with('message',"Errore durante l'eliminazione delle immagini");

            }
                
        }
    }
    public function uploadFile(Request $request){ //Funzione per caricare un file sul server
        try{
            $request->validate([

                'campoTesto' => 'required', //Campo di testo (da usare se ho campi che non vengono gestiti da nessun modello)
                'fileInput' => 'required|array|min:1|max:10', //Setto il numero massimo di file che si possono caricare insieme
                'fileInput.*' => 'image|mimes:jpeg,png,jpg,gif|max:4048' // Verifico l'estensione del file
            ], [
                'fileInput.required' => 'È necessario selezionare almeno un file.', //Messaggio che compare quando non viene soddisfatta la prima regola della validazione
                'fileInput.image' => 'È necessario utilizzare uno dei seguenti formati: jpg,png.gif' //Messaggio che compare quando non viene soddisfatta la seconda regola della validazione
            ]);

            $station = $request->input('campoTesto');
            $images = $request->file('fileInput');

            $user = Auth::user(); //Ottengo i dati dell'utente attualmente loggato

            foreach ($images as $image) { //Eseguo il ciclo di caricamento di ogni singola foto

                $image_path = 'uploads/'.$user->city.'/'.$station .'/'. $image->getClientOriginalName();
                Storage::disk('public')->put($image_path, file_get_contents($image));

            }
            return redirect()->back()->with('alert-type','success')->with('message','Immagini caricate con successo!');

        }catch(\Exception $e){
            return redirect()->route("homepage")->with('alert-type','error')->with('message','Errore durante il caricamento delle immagini');
        }
    }
}