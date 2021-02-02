<template>
    <div>
        <h4 class="text-center"><b>Pre-registro</b></h4>
        <b-card class="mb-3">
            <b-alert show variant="info">
                <b-icon-info-circle></b-icon-info-circle> <b>Importante</b> <br>
                <p>
                    Al registrar tus datos te solicitamos que ingreses un correo electrónico que utilices habitualmente, en él te haremos llegar un correo donde te informaremos si tu pre-registro ha sido aceptado o rechazado. <br>
                    Antes de guardar tu pre-registro, verifica que tus datos sean correctos, ya que de lo contrario será rechazado.
                </p>
            </b-alert>
            <b-card>
                <strong>Horario de atención</strong><br>
                <ul>
                    <li>Lunes a Viernes de 10:00 am - 5:00 pm</li>
                    <li>Sábado de 10:00 am - 1:00 pm </li>
                </ul>
                <b-icon-telephone-fill></b-icon-telephone-fill> 56 2741 1481
            </b-card>
            <h6 class="mt-2">
                <b>
                    Si necesitas ayuda para realizar tu pre-registro, puedes descargar este 
                    <a href="/student/download_tutorial">tutorial</a> para poder guiarte.
                </b>
            </h6>
        </b-card>
        <b-row>
            <b-col sm="6">
                <b-form-group label="Numero de cuenta al que se realizó el deposito">
                    <b-form-input v-model="cuenta" :disabled="load || statusCuenta"
                        type="number" required
                    ></b-form-input>
                </b-form-group>
            </b-col>
            <b-col sm="6">
                <b-button class="mt-4" variant="success" @click="checkCuenta()" pill>
                    <b-icon-arrow-right-circle-fill></b-icon-arrow-right-circle-fill> Continuar
                </b-button>
            </b-col>
        </b-row>
        <form @submit="onSubmit" enctype="multipart/form-data">
            <h5><b>Datos del alumno</b></h5>
            <b-row>
                <b-col>
                    <b-form-group label="Nombre">
                        <b-form-input v-model="form.name" :disabled="load || !statusCuenta"
                            style="text-transform:uppercase;" required
                        ></b-form-input>
                        <div v-if="errors && errors.name" class="text-danger">
                            El campo Nombre es requerido y tiene que ser igual o mayor a 3 caracteres.
                        </div>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group label="Apellidos">
                        <b-form-input v-model="form.lastname" :disabled="load || !statusCuenta"
                            style="text-transform:uppercase;" required
                        ></b-form-input>
                        <div v-if="errors && errors.lastname" class="text-danger">
                            El campo Apellidos es requerido y tiene que ser igual o mayor a 5 caracteres.
                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-group label="Correo electrónico:">
                        <b-form-input v-model="form.email"
                            type="email" required :disabled="load || !statusCuenta"
                        ></b-form-input>
                        <div v-if="errors && errors.email" class="text-danger">
                            <b>El campo Correo electrónico es requerido.</b>
                        </div>
                        <b-form-text v-if="form.email.length > 0">
                            Verifica que tu correo sea correcto ya que en él te daremos respuesta.
                        </b-form-text>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group label="Numero de teléfono">
                        <b-form-input v-model="form.telephone" :disabled="load || !statusCuenta"
                           type="number" required
                        ></b-form-input>
                        <div v-if="errors && errors.telephone" class="text-danger">
                            El campo Numero de teléfono es requerido y tiene que ser igual a 10 digitos.
                        </div>
                    </b-form-group>
                </b-col>
            </b-row>
            <b-row>
                <b-col>
                    <b-form-group label="Plantel:">
                        <b-form-select v-model="form.school"
                            :options="schools" @change="selectPlantel()"
                            required :disabled="load || !statusCuenta || !selBook"
                        ></b-form-select>
                    </b-form-group>
                </b-col>
            </b-row>
            <hr>
            <h5><b>Datos del libro</b></h5>
            <b-row>
                <b-col>
                    <b-form-group label="Libro:">
                        <b-form-select v-model="valueBook" @change="selectBook()"
                            :options="books" required :disabled="load || selSchool"
                        ></b-form-select>
                    </b-form-group>
                </b-col>
                <b-col sm="2">
                    <b-form-group label="Numero de piezas">
                        <b-form-input v-model="form.quantity" @change="setQuantity()"
                            required type="number" :disabled="load || selBook"
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col sm="2">
                    <b-form-group label="Precio del libro">
                        ${{ form.price | numeral('0,0') }}
                    </b-form-group>
                </b-col>
                <b-col sm="2">
                    <b-form-group label="Total">
                        ${{ form.a_depositar | numeral('0,0') }}
                    </b-form-group>
                </b-col>
            </b-row>
            <hr>
            <b-row>
                <b-col><h5><b>Datos del pago</b></h5></b-col>
                <b-col class="text-right">
                    <b-button variant="light" :disabled="load || selBook" @click="addComprobante()" pill>
                        <b-icon-plus-circle-fill></b-icon-plus-circle-fill> Agregar otro pago
                    </b-button>
                </b-col>
            </b-row>
            <div v-for="(comprobante, i) in form.comprobantes" v-bind:key="i">
                <div class="text-right" v-if="i > 0">
                    <b-button variant="secondary" pill @click="deleteComprobante(i)">
                        <b-icon-dash-circle-fill></b-icon-dash-circle-fill> Eliminar
                    </b-button>
                </div>
                <b-row>
                    <b-col>
                        <b-form-group label="Tipo de pago:">
                            <b-form-select v-model="comprobante.type" :disabled="load || selBook"
                                :options="types" required>
                            </b-form-select>
                        </b-form-group>
                    </b-col>
                    <b-col v-if="comprobante.type == 'transferencia'">
                        <b-form-group label="Banco:">
                            <b-form-select v-model="comprobante.bank" :disabled="load || selBook"
                                :options="banks" required
                            ></b-form-select>
                        </b-form-group>
                        <b-input v-if="comprobante.bank === 'OTRO'" v-model="specifyBank" 
                            placeholder="Especifica el nombre del banco" required 
                            style="text-transform:uppercase;"
                            @keyup="posicion = i">
                        </b-input>
                    </b-col>
                    <b-col>
                        <b-form-group v-if="comprobante.type !== null && comprobante.type == 'oxxo'" 
                            label="Folio de venta">
                            <b-form-input v-model="comprobante.folio" type="number" min="10"
                                :disabled="load || selBook" required
                            ></b-form-input>
                        </b-form-group>
                        <b-form-group v-if="comprobante.type !== null && (comprobante.type == 'practicaja' || comprobante.type == 'ventanilla')" 
                            :label="comprobante.type === 'practicaja' ? 'Folio':'Movimiento'">
                            <b-form-input v-model="comprobante.folio" type="number" min="10"
                                :disabled="load || selBook" required
                            ></b-form-input>
                        </b-form-group>
                        <div v-if="comprobante.type !== null && comprobante.type === 'transferencia'">
                            <b-form-group v-if="comprobante.bank !== null && comprobante.bank !== 'BANCOPPEL'" 
                                :label="typeMessage(comprobante.type,comprobante.bank)">
                                <b-form-input v-model="comprobante.folio" type="number" min="99"
                                    :disabled="load || selBook" required
                                ></b-form-input>
                            </b-form-group>
                            <b-form-group v-if="comprobante.bank !== null && comprobante.bank === 'BANCOPPEL'" 
                                label="Concepto">
                                <b-form-input v-model="comprobante.folio" type="text" minlength="5"
                                    :disabled="load || selBook" required
                                ></b-form-input>
                            </b-form-group>
                        </div>
                    </b-col>
                    <b-col v-if="(comprobante.type == 'practicaja' || comprobante.type == 'oxxo') && comprobante.type !== null">
                        <b-form-group label="Autorización">
                            <b-form-input v-model="comprobante.auto" :disabled="load || selBook"
                                style="text-transform:uppercase;" required
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-row>
                    <b-col>
                        <b-form-group label="Total depositado">
                            <b-form-input v-model="comprobante.total" :disabled="load || selBook"
                                required type="number" step="0.01" min="1" 
                            ></b-form-input>
                        </b-form-group>
                    </b-col>
                    <b-col>
                        <b-form-group label="Fecha del pago">
                            <b-form-datepicker required :disabled="load || selBook" v-model="comprobante.date"></b-form-datepicker>
                        </b-form-group>
                    </b-col>
                </b-row>
                <b-form-group label="Plaza o sucursal donde se realizo el pago:"
                    v-if="comprobante.type !== 'transferencia' && comprobante.type !== null">
                    <b-form-input v-model="comprobante.plaza" :disabled="load || selBook"
                        style="text-transform:uppercase;" required
                    ></b-form-input>
                </b-form-group>
            </div>
            <hr>
            <h5><b>Comprobante(s)</b></h5>
            <b-form-group label="Subir comprobante(s) de pago (Formato imagen o pdf)">
                    <input 
                        :disabled="load || selBook" type="file" id="archivoType" 
                        multiple="multiple" v-on:change="fileChange">
                    <label for="archivoType"><b-icon-upload></b-icon-upload> Seleccionar archivo(s)</label>
                    <ol type="1" v-for="(file, i) in form.files" v-bind:key="i">
                        <li>
                            {{ file.name }} 
                            <b-button pill size="sm" @click="deleteFile(i)" variant="secondary">
                                <b-icon-dash-circle-fill></b-icon-dash-circle-fill> Eliminar
                            </b-button>
                        </li> 
                    </ol>
            </b-form-group>
            <hr>
            <b-row class="mt-3 mb-5">
                <b-col>
                    <b-alert class="mt-2" v-if="load" show variant="info">
                        <b-spinner small type="grow"></b-spinner> Guardando...<br>
                        Estamos guardando tus datos, por favor no cierres esta pagina hasta que terminemos.
                    </b-alert>
                </b-col>
                <b-col class="text-right" sm="3">
                    <b-button pill block :disabled="load || selBook" variant="success" type="submit">
                        <i class="fa fa-plus-circle"></i> Guardar
                    </b-button>
                </b-col>
            </b-row>
        </form>
    </div>
</template>

<script>
// SWEETALERT
import swal from 'sweetalert';
export default {
    props: ['registers'],
    data(){
        return {
            load: false,
            types: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'ventanilla', text: 'DEPOSITO EN VENTANILLA (EFECTIVO)'},
                { value: 'practicaja', text: 'DEPOSITO EN PRACTICAJA (EFECTIVO)'},
                { value: 'transferencia', text: 'TRANSFERENCIA'},
                { value: 'oxxo', text: 'OXXO'}
            ],
            banks: [
                { value: null, text: 'Selecciona una opción', disabled: true },
                { value: 'BANAMEX', text: 'BANAMEX'},
                { value: 'BANCO AZTECA', text: 'BANCO AZTECA'},
                { value: 'BANCOMER', text: 'BANCOMER'},
                { value: 'BANCOPPEL', text: 'BANCOPPEL'},
                { value: 'BANBAJIO', text: 'BANBAJIO'},
                { value: 'BANORTE', text: 'BANORTE'},
                { value: 'HSBC', text: 'HSBC'},
                { value: 'SANTANDER', text: 'SANTANDER'},
                { value: 'SCOTIABANK', text: 'SCOTIABANK'},
                { value: 'OTRO', text: 'OTRO'},
            ],
            specifyBank: null,
            form: {
                name: '', lastname: '', email: '',
                telephone: null, school: null, book: null,
                quantity: 1, price: 0, a_depositar: 0,
                files: [],
                comprobantes: [{
                    type: null, folio: '', auto: '', bank: null,
                    total: null, date: null, plaza: ''
                }]
            },
            optSchools: this.registers,
            schools: [],
            errors: {},
            selSchool: true,
            books: [],
            selBook: true,
            valueBook: null,
            position: null,
            a_depositar: null,
            cuenta: null,
            statusCuenta: false,
            errors: {},
            posicion: null
        }
    },
    filters: {
        
    },
    created: function(){
        this.schools.push({
            value: null,
            text: 'Selecciona una opción',
            disabled: true
        });
        this.optSchools.forEach(school => {
            this.schools.push({
                value: school.id,
                text: `${school.name}`
            });
        });
    },
    methods: {
        fileChange(e){
            this.acum_total();
            if(this.a_depositar >= this.form.a_depositar){
                var fileInput = document.getElementById('archivoType');
                var allowedExtensions = /(\.jpg|\.jpeg|\.pdf|\.png)$/i;
                if(allowedExtensions.exec(fileInput.value)){
                    let files = e.target.files || e.dataTransfer.files;
                    if (!files.length) return;
                    
                    if((this.form.files.length + files.length) <= this.form.comprobantes.length){
                        for (let i = files.length - 1; i >= 0; i--) {
                            this.form.files.push(files[i]);
                        }
                        
                        document.getElementById("archivoType").value = [];
                    }
                    else {
                        swal("Revisar número de pagos", `Solo tienes ${this.form.comprobantes.length} pago(s) registrado(s). El número de archivos debe corresponder al número de pagos que registres.`, "warning");
                    }
                } else {
                    swal("Revisar formato de archivo", "Revisar formato de archivo", "Formato de archivo no permitido, solo puede ser en formato imagen (jpg, jpeg, png) o pdf.", "warning");
                }
            } else {
                swal("Revisar total", "El total de los datos de pago que registraste tiene que ser igual o mayor al total de tu compra. Por favor revisa y corrige para poder continuar.", "warning");
                this.load = false;
            }
        },
        onSubmit(e){
            e.preventDefault();
            this.load = true;
            this.acum_total();
            if(this.a_depositar >= this.form.a_depositar &&
                this.form.files.length === this.form.comprobantes.length){
                let fd = this.attributes();
                axios.post('/student/preregister', fd, { headers: { 'Content-Type': 'multipart/form-data' } }).then(response => {
                    if(response.data === 3){
                        swal("Guardado", "Tus datos han sido guardados correctamente. Aproximadamente en un lapso de 48 horas hábiles te haremos llegar un correo electrónico donde te notificaremos si tu pre-registro ha sido validado. Gracias.", "success")
                            .then((value) => {
                                location.href = '/student/register';
                            });
                    }
                    if(response.data === 1) {
                        swal("Pre-registro en proceso", "Ya tienes un pre-registro pendiente, te haremos llegar un correo electrónico donde te notificaremos si tu pre-registro ha sido validado. Gracias.", "info");
                    } 
                    if(response.data === 2) {
                        swal("Pre-registro aceptado", "Tu pre-registro ya ha sido aceptado anteriormente, si no te llego un correo electrónico de confirmación, por favor contactanos a este correo registro.pagos@majesticeducacion.com.mx", "info");
                    }
                    this.errors = {};
                    this.load = false;
                }).catch(error => {
                    // PENDIENTE
                    this.load = false;
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors || {};
                    } else {
                        swal("Error al guardar pre-registro", "Ocurrió un error mientras intentamos guardar tu pre-registro, por favor vuelve a intentar presionando en el botón Guardar.", "error")
                    }
                });
            } else {
                swal("Revisar pago(s)", "Por favor revisa que el total de los datos de pago que registraste sea igual o mayor al total de tu compra y/o que el número de comprobantes sea el mismo número de datos de pago registrados.", "warning");
                this.load = false;
            }
        },
        attributes(){
            let formData = new FormData();
            if(this.specifyBank !== null) this.form.comprobantes[this.posicion].bank = this.specifyBank.toUpperCase();
            formData.append('name', this.form.name);
            formData.append('lastname', this.form.lastname);
            formData.append('email', this.form.email);
            formData.append('telephone', this.form.telephone);
            formData.append('school', this.form.school); 
            formData.append('book', this.form.book);
            formData.append('quantity', this.form.quantity);
            formData.append('price', this.form.price);
            formData.append('a_depositar', this.form.a_depositar);
            formData.append('files', this.form.files);
            formData.append('comprobantes', JSON.stringify(this.form.comprobantes));
            for (var i = 0; i < this.form.files.length; i++) {
                let files = this.form.files[i];
                formData.append('files[]', files);
            }
            
            return formData;
        },
        selectPlantel(){
            this.selSchool = true;
            axios.get('/schools/get_books', {params: {school_id: this.form.school}}).then(response => {
                this.books = [];
                this.form.book = null;
                this.school_select(response.data);
                this.selSchool = false;
            }).catch(error => {
                this.selSchool = false;
            });
        },
        school_select(books){
            this.books.push({
                value: null,
                text: 'Selecciona una opción',
                disabled: true
            });
            books.forEach(book => {
                this.books.push({
                    value: book,
                    text: `${book.name}`
                });
            });
        },
        selectBook(){
            this.form.book = this.valueBook.name;
            this.form.quantity = 1;
            this.form.price = this.valueBook.pivot.price;
            this.form.a_depositar = parseFloat(this.form.price) * parseInt(this.form.quantity);
            this.selBook = false;
        },
        addComprobante(){
            this.form.comprobantes.push({
                    type: null, folio: '', auto: '', bank: null, total: null, 
                    date: null, plaza: '', file: null
                });
        },
        setQuantity(){
            if(parseInt(this.form.quantity) < 1) this.form.quantity = 1;

            this.form.a_depositar = parseFloat(this.form.price) * parseInt(this.form.quantity);
        },
        acum_total(){
            this.a_depositar = 0;
            this.form.comprobantes.forEach(comprobante => {
                this.a_depositar += parseFloat(comprobante.total);
            });
        },
        deleteComprobante(i){
            this.form.comprobantes.splice(i, 1);
        },
        deleteFile(i){
            this.form.files.splice(i, 1);
        },
        checkCuenta(){
            // 0172427206
            if(this.cuenta === '0172427206' || this.cuenta === '012180001724272063' || this.cuenta === '5204165073723995' || this.cuenta === '002180701156103586') this.statusCuenta = true;
            else {
                this.statusCuenta = false;
                swal("Numero de cuenta incorrecto", "El numero de cuenta que ingresaste no corresponde al nuestro.", "error");
            }
        },
        typeMessage(value1, value){
            // if(value1 === 'ventanilla') {
            //     if(value === 'BANCOMER') return 'Movimiento';
            //     if(value === 'BANCOPPEL') return 'Referencia numerica';
            //     if(value === 'SANTANDER') return 'Concepto';
            //     else return 'Referencia';
            // }
            if(value1 === 'transferencia') {
                if(value === 'BANCOMER') return 'Folio';
                if(value == 'BANCO AZTECA' || value == 'BANORTE' || value == 'HSBC' || value == 'BANBAJIO' || value == 'SANTANDER') 
                    return 'Referencia';
                if(value === 'BANAMEX') return 'Referencia numerica';
                else return 'Referencia';
            }
            // if(value1 === 'practicaja') return 'Folio';
        }
    }
}
</script>