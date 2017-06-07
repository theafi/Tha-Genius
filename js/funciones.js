            /* Nota: Hay funciones que no figuran aquí porque requieren ser parseadas por PHP (por ejemplo para enviar tokens)

            */
            

            function cerrarNuevo() { // Se carga todo lo que hay dentro de la fila 
                document.getElementById("dominio").innerHTML = ''
            }
            function checkPass(f) {
            
                // Guardo las contrase;as en variables
                var pass1 = f.elements["password"];
                var pass2 = f.elements["passwordcheck"];
                // Mensaje de confirmación
                var message = document.getElementById('error');
                //Los colores (que serán rojo para los mensajes de error)
                var badColor = "#ff6666";
                //Compara los valores en las contrase;as
                if(pass1.value == pass2.value){
                    //Las contraseñas coinciden
                    // Para que pueda enviar el formulario sin cambiar la contrase;a necesariamente tengo que permitir que si el valor es 0 que devuelva true
                 /*   if (pass1.value.length === 0 && pass2.value.length === 0) {
                        return (true); // Aquí no */
                    if(pass1.value.length < 6 && pass2.value.length < 6) { //Comprueba si la contrasena es menor de seis caracteres
                        pass1.style.backgroundColor = badColor;
                        pass2.style.backgroundColor = badColor;
                        message.style.color = badColor;
                        message.innerHTML = "La contraseña tiene que ser de 6 caracteres mínimo";
                        return (false);
                    } else {
                        return (true);
                    }
                    
                    
                } else{
                    //Las contrasenas no coinciden. Notifica al usuario y pinta el campo erroneo.
                    pass2.style.backgroundColor = badColor;
                    message.style.color = badColor;
                    message.innerHTML = "Las contraseñas no coinciden";
                    return (false);
                }
        }  
            function checkPassModificada(f) { //Esta función es para la modificación de usuarios. La principal diferencia es que te deja enviar el formulario si los dos campos de contrasena estan vacios
            
                // Guardo las contrase;as en variables
                var pass1 = f.elements["password"];
                var pass2 = f.elements["passwordcheck"];
                // Mensaje de confirmación
                var message = document.getElementById('error');
                //Los colores (que serán rojo para los mensajes de error)
                var badColor = "#ff6666";
                //Compara los valores en las contrase;as
                if(pass1.value == pass2.value){
                    //Las contraseñas coinciden
                    // Para que pueda enviar el formulario sin cambiar la contrase;a necesariamente tengo que permitir que si el valor es 0 que devuelva true
                    if (pass1.value.length === 0 && pass2.value.length === 0) {
                        return (true);
                    } else if(pass1.value.length < 6 && pass2.value.length < 6) {
                        pass1.style.backgroundColor = badColor;
                        pass2.style.backgroundColor = badColor;
                        message.style.color = badColor;
                        message.innerHTML = "La contraseña tiene que ser de 6 caracteres mínimo";
                        return (false);
                    } else {
                        return (true);
                    }
                    
                    
                } else{
                    pass2.style.backgroundColor = badColor;
                    message.style.color = badColor;
                    message.innerHTML = "Las contraseñas no coinciden";
                    return (false);
                }
        }  
           function contraseñaAleatoria() { // Genera una contrasena aleatoria. No la anade automáticamente al campo contrasena porque entra en conflicto con la "libreria" pwstrength-bootstrap.
                var randomstring = btoa(Math.random()).slice(-10); // Genera números aleatoriamente y los convierte en un string en Base64. Conservo los 10 últimos caracteres del string únicamente para generar una contraseña fuerte.
                document.getElementById("randompass").innerHTML = '<span class="input-group-btn"><button type="button" onclick="contraseñaAleatoria();" class="btn btn-sm" id="randompassword">Generar contraseña</button></span><input type="text" value="' + randomstring + '" class="form-control" readonly>';
            }
            function previsualizarEmail() { // Permite ver el email antes de que se cree juntando los campos Nombre de usuario y Dominio. De paso hace un seguimiento de expresiones regulares para que no se pueda insertar ningun caracter prohibido
                var nombre = document.getElementById("usuario")
                var dominio = document.getElementById("dominio");
                document.getElementById("demo").value = nombre.value.replace(/[^A-Za-z0-9._%+-]/g, '') + '@' + dominio.options[dominio.selectedIndex].text;
            }
            function opcionesDeAdministrador() { // Genera un campo si pulsas Sí en hacer usuario al administrador para que le anadas una contrasena para autenticarse en el portal. La contrasena es obligatoria. 
                var codigoHTML = '<div class="form-group">'
                               + '<input type="password" name="passwordadmin" class="form-control" id="passwordadmin" placeholder="Contraseña para el administrador" minLength="6" required>  '
                            +'<small>Es aconsejable utilizar una contraseña distinta a la anterior</small>'
                            +'</div>'
                           + '<div class="col-sm-4 col-sm-offset-2" style="">'
                               + '<div class="form-group">'
                                   + '<div class="pwstrength_viewport_progress"></div> '                       
                                +'</div>'
                         +  ' </div>';
                document.getElementById("admin").innerHTML = codigoHTML;
            }
            function cerrarRedireccion() { // Se carga el campo de dentro
                document.getElementById("nuevoForwarding").innerHTML = ''
            }

              