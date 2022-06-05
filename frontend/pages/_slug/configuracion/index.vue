<template>
    <section>
        <Breadcrumb 
        :urls="[{ path: 'configuracion', name: 'Configuración', last: true }]"/>

        <h1>Configuración</h1>
    </section>
</template>

<script>
export default {
    layout: 'client',
    middleware: 'auth',
}
</script>

<style>

</style>