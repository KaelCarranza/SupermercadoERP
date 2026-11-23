=================================================
Etapa #1
=================================================
1. instalar primero XAMPP y activar los module Apache y MySQL (se deben ver en color verde)
2. ingresar a http://localhost/phpmyadmin/
3. En el menu lateral tocar donde dice "Nueva" y en el nombre de la base de datos escribir inventech_db 
seguido tocar el boton de crear (sin tocar nada mas)
4. abrir la base de datos creada en el menu lateral, y seguido en el menu superior tocar donde dice importar y en el primer menu que aparece tocar donde dice "Elegir Archivo" y seleccionar el archivo de la base de datos .sql
5. Bajar hasta el final sin tocar nada mas y tocar el boton de importar,
6. Con eso la base de datos deberia funcionar (los usuarios estan abajo, pero crear uno propio)


=================================================
Etapa #2
=================================================

1. Opcion 1. Ingresar al explorador de archivos, en el el disco local C, buscar la carpeta "xampp" y dentro de esa carpeta buscar otra carpeta llamada "htdocs"
2. Opcion 2. Apretar las teclas Windows + R en el teclado y en el ejecutar, copiar y pegar -> C:\xampp\htdocs
3. Cualquiera de las 2 opciones: deben pegar o extraer la carpeta del proyecto en la carpeta que se abre

para abrir y ver el proyecto es en Chrome, edge o cualquier navegador
en esta direccion http://localhost/proyecto/login.php
y ya podran ver el proyecto e ingresar

=================================================
Etapa #3
=================================================

1. Abrir la carpeta del proyecto (Que se guardo como se explica en la etapa 2) en visual Studio code y empezar a programar
2. Se que el proyecto se puede ver enrredado pero deben programar cada cosa en la carpeta /controllers/compras, facturacion o inventario (usuarios ya esta listo)
3. Los demas archivos php son las vistas (que es puro html) que es donde se llaman los controladores del paso 2 
4. Pueden guiarse con usuarios.php y los controladores de la carpeta usuarios (o utilizar la IA para mas facilidad)

=================================================
Usuarios
=================================================

User: Kael 
Pass: Kael1234

User: Admin
Pass: Admin1234

Se bloquea a un usuario incorrecto, o 3 intentos de contraseña fallidos
=================================================