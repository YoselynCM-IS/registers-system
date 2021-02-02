<template>
    <div>
        <b-form @submit.prevent="!edit ? onSubmit():onUpdate()">
            <b-form-group label="Nombre de la escuela:">
                <b-form-input v-model="school.name" required :disabled="load"
                    style="text-transform:uppercase;">
                </b-form-input>
                <div v-if="errors && errors.name" class="text-danger">La escuela ya se encuentra registrada.</div>
            </b-form-group>
            <div class="text-right">
                <b-button pill :disabled="load" variant="success" type="submit">
                    <b-icon-plus-circle></b-icon-plus-circle> Guardar
                </b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
export default {
    props: ['school', 'edit'],
    data(){
        return {
            load: false,
            errors: {}
        }
    },
    methods: {
        onSubmit(){
            this.load = true;
            axios.post('/schools/new_school', this.school).then(response => {
                this.load = false;
                this.$emit('updateSchools', response.data);
            })
            .catch(error => {
                this.load = false;
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
            });
        },
        onUpdate(){
            this.load = true;
            axios.put('/schools/update_school', this.school).then(response => {
                this.load = false;
                this.$emit('updateSchools', response.data);
            })
            .catch(error => {
                this.load = false;
                if (error.response.status === 422) {
                    this.errors = error.response.data.errors || {};
                }
            });
        }
    }
}
</script>