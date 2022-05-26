<?php

namespace App\Http\Controllers;
use App\Models\Panier;
use App\Models\Produit;
use Illuminate\Http\Request;
use App\Models\Commande;

class PanierController extends Controller
{
    public function ajouterPanier(Request $request){

        $quantite = $request->quantite;
        $taille = $request->taille;
        $produit_id = $request->produit_id;
        $user_id = $request->user_id;
        $panier =  new Panier();
        $panier->quantite = $quantite;
        $panier->produit_id = $produit_id;
        $panier->user_id = $user_id;
        $panier->taille = $taille;
        $panier->save();
        return response()->json("le panier est bien enregistrer");

    }

    public function getPanier($user_id){
        $panier = Panier::where(["user_id"=>$user_id,"commande_id"=>0])->get();   
     
        $list_produitPanier = array();
        $prixTot = 0;
        $x=0;
        foreach($panier as $pan){
            $produit = Produit::find($pan->produit_id);
            if($produit->is_promo==1){
                $x=$produit->nouveauPrix;
            }else{
                $x=$produit->prix;
            }
            $prixTot = $prixTot + $x*$pan->quantite;
            array_push($list_produitPanier,(object)[
                'id'=>$pan->id,
                'quantite'=>$pan->quantite,
                'taille'=>$pan->taille,
                'produit_id'=>$pan->produit_id,
                'user_id'=>$pan->user_id,
                'created_at'=>$pan->created_at,
                'updated_at'=>$pan->updated_at,
                'produit_nom'=>$produit->nom,
                'produit_img'=>$produit->image1,
                'produit_prix'=>$x,
                'produit_quantite'=>$produit->quantite,
                'total'=>$x*$pan->quantite,
               
            ]);
        
            
          
    }
    

    return (object)['prix_tot'=>$prixTot,'list_produitPanier'=>$list_produitPanier];

}


public function deletePanier(Panier $panier){

    $panier->delete();
        return response()->json("le panier est supprimer");
    
}


public function ViderPanier($user_id){
    $value = 0;
    $panier = Panier::where(['commande_id'=>$value,'user_id'=>$user_id])->get();
   
    return $panier;
}
public function maxProduit(){
    $valeur = 0;
    $somProd = 0;
    $sommeProduit=array();
    $paniers = Panier::where('commande_id','<>',$valeur)
    ->get()->groupBy("produit_id");

    foreach($paniers as $panier){
       $somProd =  $panier->sum('quantite');  
       foreach($panier as $pan){
        $id = $pan->produit_id;
        $produit = Produit::where('id',$id)->first();
       }
       array_push($sommeProduit,(object)[
        'quantite_total'=>$somProd,
        'produit_nom'=>$produit->nom,
        'produit_image'=>$produit->image1,
        'produit_detail'=>$produit->detail,

       ]);
      
    }
    return max($sommeProduit);
 
}

public function minProduit(){
    $valeur = 0;
    $somProd = 0;
    $sommeProduit=array();
    $paniers = Panier::where('commande_id','<>',$valeur)
    ->get()->groupBy("produit_id");

    foreach($paniers as $panier){
       $somProd =  $panier->sum('quantite');  
       foreach($panier as $pan){
        $id = $pan->produit_id;
        $produit = Produit::where('id',$id)->first();
       }
       array_push($sommeProduit,(object)[
        'quantite_total'=>$somProd,
        'produit_nom'=>$produit->nom,
        'produit_image'=>$produit->image1,
        'produit_detail'=>$produit->detail,

       ]);
      
    }
    return min($sommeProduit);
 
}


public function getCommandeUser($user_id){
    $valeur = 0;
    $panier = Commande::with('paniers')->get();
    
   
     return response()->json($this->formater($panier));
    }


public function formater($panier){
    $t = count($panier);
    $p = Produit::all();
    $t1 = count($p);
    for($i=0; $i<$t; $i++){
        $data['id']=$panier[$i]->id;
        $data['prix']=0;
        $data['commande']=$panier[$i]->paniers;
        $t3 = count($panier[$i]->paniers);
        for($z=0; $z<$t3; $z++){
        for($j=0; $j<$t1; $j++){
            if($p[$j]->id == $panier[$i]->paniers[$z]->produit_id){
                $panier[$i]->paniers[$z]->nom_produit = $p[$j]->nom;
                
                if($p[$j]->is_promo ==0){
                $panier[$i]->paniers[$z]->prix_unitaire = $p[$j]->prix;
        
                }
                else{
                    $panier[$i]->paniers[$z]->prix_unitaire = $p[$j]->nouveauPrix;
                }
 
                $data['prix']= $data['prix']+ $panier[$i]->paniers[$z]->prix_unitaire*$panier[$i]->paniers[$z]->quantite;
            }
        }
    }
        
        $x[$i]=$data;
        }
       
        return $x;
    }



}

