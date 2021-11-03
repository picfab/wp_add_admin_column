# wp_add_admin_column
Ajouter des colonne supplémentaire dans wordpress

## exemple 1 :Basic exemple 
	
  ```
    new_column('yt','Facebook',false,['post']);	
  ```
  
## exemple 2 : Ajouter le l'ordre
  ```
    new_column('yt','Facebook',true,['post']);	
  ```
  
## exemple 3 : Overider le rendu de la valeur de chaque ligne

```
  add_action('acf/init',function(){
    new_column('yt','Facebook',true,['post'],'newcontent');	
  });

  function newcontent($content){
    return '<h1>'.$content.'</h1>';
  }
```
