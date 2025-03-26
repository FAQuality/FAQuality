# FAQuality - Plugin de FAQ para WordPress

**FAQuality** es un plugin para WordPress que permite gestionar un sistema de preguntas frecuentes (FAQ) de forma jerárquica. Además, incluye un formulario al final de cada FAQ para que los usuarios puedan enviar sus dudas, las cuales generan un correo personalizado.

## Características

- Creación y administración de FAQs de manera jerárquica mediante categorías.
- Generación de shortcodes dinámicos para insertar FAQs en cualquier parte del sitio.
- Formulario integrado en cada FAQ para que los usuarios envíen sus preguntas adicionales.
- Envío automático de correos personalizados con las dudas de los usuarios.

## Instalación

1. Descarga el plugin o clona el repositorio en el directorio `wp-content/plugins/` de tu instalación de WordPress.
2. Activa el plugin desde el panel de administración de WordPress en la sección **Plugins**.

## Uso

### Administración
Desde el panel de administración de WordPress, puedes acceder a las siguientes opciones:

- **Crear una nueva FAQ**: `admin.php?page=Nuevo_FAQ`
- **Crear una categoría**: `admin.php?page=FAQ_New_Categoria`
- **Revisar preguntas de usuarios**: `admin.php?page=Dudas`

### Generación de Shortcodes
Para insertar FAQs en tu sitio, puedes generar un shortcode dinámico seleccionando una categoría desde el panel de administración:

```html
<select name="id_cat" id="id_cat">
    <option value="1">Ejemplo de Categoría</option>
</select>
