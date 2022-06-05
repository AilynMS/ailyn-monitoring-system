import { extend } from "vee-validate";
import { required, email, image, alpha_spaces } from "vee-validate/dist/rules";

extend('image', {
    ...image,
    message: "La imagen no es de un tipo válido"
});


extend('alpha_spaces', {
    ...alpha_spaces,
    message: "El contenido del campo no es válido"
});

extend('email', {
    ...email,
    message: "Introduzca una dirección de correo electrónico válida"
});

extend('required', {
    ...required,
    message: "El campo es requerido"
});

extend('min', {
    params: ['target'],
    validate(value, { target }) {
      return value >= target;
    },
    message: `El valor del campo debe ser mayor a 0`
});

extend('verify_password', {
    validate: value => {
        const strongRegex = new RegExp("^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\\d\\x])(?=.*[!$#%]).*$");
        return strongRegex.test(value);
    },
    message: "La contraseña debe contener al menos: 6 caracteres, la letra (d) o (x), 1 numero y un carácter especial (!, ¡, ?, ., _, &?, etc.)"
});

extend('password', {
    params: ['target'],
    validate(value, { target }) {
      return value === target;
    },
    message: 'Las contraseñas no coinciden'
});

extend('url', {
    validate: value => {
        const strongRegex = new RegExp("^https?:\/\/[\w\-]+(\.[\w\-]+)+[/#?]?.*$");
        return strongRegex.test(value);
    },  
    message: "La respuesta debe ser válida"
});

extend('url_validate', {
    validate: value => {
        const Regex = new RegExp("^http");
        let result = Regex.test(value);
        if(result) return false; 
        return true;
    },
    message: "Ingrese la url sin http o https"
});

extend('domain_validate', {
    validate: value => {
        const Regex = new RegExp("/");
        let result = Regex.test(value);
        if(result) return false; 
        return true;
    },
    message: "Ingrese solo el dominio, sin /"
});

extend('path_validate', {
    validate: value => {
        const Regex = new RegExp("^/.*./$", "g");
        return Regex.test(value);
    },
    message: "El path debe empezar y terminar con /"
});