<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Validation\Rules;
use Illuminate\Database\Eloquent\Model; //Da importare solo se non si ha definito un altro modello di base (che può anche includere questo), in tal caso includi quel file


class SettingsModel extends Model //Questa tabella nel db ha un solo record che puo essere aggiornato solamente
{
    use HasFactory; //Per usare le factory del db
    const MAX_LENGTH = 255; //Indica la lunghezza massima delle stringa (la definisco io non è già inclusa in laravel)
    protected $table = "nome_tabella"; //Indica il nome della tabella nel db a cui è collegato questo modello
    
    public $timestamps = true;
    protected $connection = 'mysql_connection'; //Definizione di quale connessione utilizzare (per definire a quale db connettersi quando si usa il model) non sempre deve essere usato
    protected $primary_key = 'id'; //Definizione del campo id che può essere omesso nei campi fillable
    public $incrementing = true; //In questo modo dichiaro che la chiave primaria e auto-incrementante
    protected $casts = [ // Posso evitare di mettere questi campi nelle rules e nei fillable
        'cifra_totale' => 'float', //Converte cifra totale da stringa a numero (se è possibile) 
        'password' => 'hashed',
        'ultimo_accesso'=>'date',
    ];

    

    protected $fillable = [ //Definizione dei campi che compongono la tabella del DB e che devono essere inseriti dall'utente nella richiesta da inviare al DB
        'campo1',
        'campo2',
        'campo3',
        'campo4',
        'password',
        'campo5',
        'data',
        'orario',
        'valore'
    ];


    //Non è pratica comune definire le regole di validazione nel modello (di solito si fa nel controller che utilizza il model), ma cosi sembra più ordinato
    protected function getRules(): array{ //Funzione per definire regole di validazione dei campi del modello
        return
        [
            'campo1' => ['nullable', 'string', 'max:' . self::MAX_LENGTH], //Definizione di un campo che può essere omesso di tipo stringa
            'campo2' => ['nullable', 'integer'], //Definizione di un campo che può essere omesso di tipo intero
            'campo3'=> ['nullable', 'digits:5'], //Campo che può essere omesso in cui devono essere inseriti 5 numeri
            'campo4' => ['nullable', 'string','email', 'max:' . self::MAX_LENGTH], //Campo che può essere omesso e che contiene una mail (controlla quindi ad esempio se c'è la @)
            'password' => ['required', 'confirmed', Rules\Password::defaults()], //Definizione del campo password (che richiede conferma) obbligatorio
            'campo5' => ['unique:nome_tabella','required','integer'], //Definizione di un campo unico (non possono esserci due valori uguali nella stessa tabella per questo campo)
            'data' => ['required', 'date'], //Definizione di una campo obbligatorio data
            'data2' => ['required', 'date_format:d/m/Y'], //Definizione del campo obbligatorio data specificando il formato della data
            'orario' => ['required','date_format:H:i:s'], //Attenzione perche anche se invii solo con minuti, nel db vengono salvati anche i secondi (messi sempre a 00)
            'valore' => ['required','numeric'] //Definizione di un campo che ha valore numerico (di solito si usa per numeri con la virgola)
        ];

    }
}
