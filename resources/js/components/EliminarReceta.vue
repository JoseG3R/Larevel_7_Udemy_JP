<template>
<input 
    type="submit" 
    class="btn btn-danger w-100 d-block mb-2"
    value="Eliminar ×"
    @click="eliminarReceta"  >
</template>

<script>
export default {
    props:['recetaId'],
    methods: {
        eliminarReceta(){
            this.$swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No!'
            }).then((result) => {
                
            if (result.isConfirmed) {
                const params = {
                    id: this.recetaId
                }
                //Envíar peticion al servidor
                axios.post(`/recetas/${this.recetaId}`, {params, _method: 'delete'})
                .then(respuesta => {
                    this.$swal({
                        title: 'Receta Eliminada',
                        text: 'Se eliminó la receta',
                        icon: 'success'
                    });
                    //Eliminar del DOM
                    this.$el.parentNode.parentNode.parentNode.removeChild(this.$el.parentNode.parentNode);
                })
                .catch(error => {
                    console.log(error);
                })
            
            }
            })
        }
    }    
}
</script>