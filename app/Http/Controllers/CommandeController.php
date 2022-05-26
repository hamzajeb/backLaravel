<?php

namespace App\Http\Controllers;
use App\Models\Panier;
use App\Models\Produit;
use App\Models\User;
use App\Models\Commande;


use Illuminate\Http\Request;

class CommandeController extends Controller
{
    public function AjouterCommande(Request $request){
        $subTotal = 0;
        $user = User::find($request->user_id);
        $element = Panier::find($request->user_id);
        $commande = new Commande;
        $commande->user_id = $request->user_id;
        $commande->save();
        $value = 0;
        $produitsPanier = Panier::where(['commande_id'=>$value,'user_id'=>$user->id])->get();
        Panier::where(['commande_id'=>$value,'user_id'=>$user->id])->update(['commande_id'=>$commande->id]);
        foreach($produitsPanier as $produitPanier){
        $produit  = Produit::find($produitPanier->produit_id);
        $produit->update(['quantite'=>$produit->quantite - $produitPanier->quantite]);
        
        }
    
    }
}
