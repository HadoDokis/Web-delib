#/bin/sh

WEBDELIB_PATH="/var/www/webdelib"
ENCODAGE="utf"
clear


cd $WEBDELIB_PATH/app
file --mime * | grep -i $ENCODAGE

cd $WEBDELIB_PATH/app/config
file --mime * | grep -i $ENCODAGE

cd $WEBDELIB_PATH/app/config/sql
file --mime * | grep -i $ENCODAGE

cd $WEBDELIB_PATH/app/controllers
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/controllers/components
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/models
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/vendors/shells
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/acteurs   
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/collectivites  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/compteurs      
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/droits    
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/infosupdefs       
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/layouts  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/natures  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/postseances  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/scaffolds  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/sequences  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/themes       
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/typeseances
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/circuits  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/commentaires  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/deliberations  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/elements  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/helpers  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/infosuplistedefs  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/models   
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/pages    
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/profils      
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/seances    
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/services   
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/typeacteurs  
file --mime * | grep -i $ENCODAGE
cd $WEBDELIB_PATH/app/views/users
file --mime * | grep -i $ENCODAGE
