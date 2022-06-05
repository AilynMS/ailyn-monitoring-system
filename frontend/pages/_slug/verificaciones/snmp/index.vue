<template>
  <div class="home-wrapper">

    <Breadcrumb 
      v-if="!isEmpty" 
      :urls="[{ path: 'snmp', name: 'Verificaciones SNMP', last: true }]"/>

    <Notification
        v-if="error.status" 
        type="error"
        :showIcon="true"
        classes="text-sm py-6" 
        :errorTitle="error.message" />

    <!-- if isEmpty -->
    <EmptyScreen title="" subtitle="" type="SMNP" v-if="isEmpty">
          <Button
            @click="toggleModal" 
            type="primary-light" 
            styles="outline-none rounded-md my-auto ml-auto mb-4 sm:mb-0">
            Añadir verificación
          </Button>
    </EmptyScreen>

    <SearchBar v-if="!isEmpty" @onSearch="handleSearch($event)" />

    <AmsTable v-if="!isEmpty" :header="[
      'Nombre', 
      'Host',
      'Oid',
      'Version',
      'Intervalo de chequeo',
      'Estado',
      'Acciones']">
      
      <template v-if="showSkeleton">
        <AmsSkeleton/>
        <AmsSkeleton/>
        <AmsSkeleton/>
      </template>

      <AmsRow v-for="(val, index) in searchFilter" :key="index">
      
        <AmsColumn :title="val.name"> {{ val.name | short(20) }} </AmsColumn>
        <AmsColumn :title="val.target">{{ val.target | short(20) }}</AmsColumn>
        <AmsColumn :title="val.oid">{{ val.oid | short(20) }}</AmsColumn>
        <AmsColumn>{{ val.version }}</AmsColumn>
        <AmsColumn>{{ val.interval + ' minutos' }}</AmsColumn>

        <AmsColumn>
          <div class="toggler flex items-center justify-center w-full">
              <label :for="index + '_toggle'" class="flex items-center cursor-pointer">
                  <div class="relative">
                      <input 
                          value="checked"
                          v-model="val.status"
                          @change="handleStatus(val)"
                          :id="index + '_toggle'" 
                          type="checkbox" 
                          class="hidden" />
                          
                      <div class="toggle__line w-10 h-4 bg-gray-300 rounded-full shadow-inner"></div>
                      <div class="toggle__dot absolute w-6 h-6 bg-white rounded-full shadow inset-y-0 left-0"></div>
                  </div>
              </label>
          </div> 
        </AmsColumn>

        <AmsColumn :hoverable="true">
          <nuxt-link 
            class="bg-primary-light-hoverable px-2 py-1 rounded-md text-sm text-primary cursor-pointer outline-none hover:text-white mr-1 ease duration-50 transition" 
            title="Estadísticas" 
            :to="`/${$auth.user && $auth.user.tenant.slug}/verificaciones/snmp/${val.id}/estadisticas`"> 
            <fa icon="chart-bar" /> 
          </nuxt-link>

          <a 
            @click="handleEdit(val)"
            class="bg-green-200 hover:bg-green-600 px-2 py-1 rounded-md text-sm text-green-600 cursor-pointer outline-none hover:text-white mr-1 ease duration-50 transition" 
            title="Modificar">
            <fa icon="pen" /> 
          </a>

          <a 
            @click.prevent="enableConfirm(val.id)"
            class="bg-red-200 hover:bg-red-600 px-2 py-1 rounded-md text-sm text-red-600 cursor-pointer outline-none hover:text-white ease duration-50 transition" 
            title="Eliminar"> 
            <fa icon="trash" /> 
          </a>
        </AmsColumn>
        
      </AmsRow>
    </AmsTable>

    <section class="w-full flex justify-between flex-col-reverse sm:flex-row mt-3">
      <Pagination 
        v-if="meta.last_page > 1" 
        :meta="meta" 
        @pagination:switched="switchPage" />

      <Button
        v-if="!isEmpty"
        @click="toggleModal" 
        type="primary" 
        styles="outline-none rounded-md my-auto ml-auto mb-4 sm:mb-0">
        Añadir verificación
      </Button>
    </section>

    <Modal
      v-if="showModal"
      @onCancel="toggleModal"
      @onSubmit="handleSave"
      :isLoading="isLoading"
      :title="getModalTitle"
      description="Completa los siguientes campos obligatorios"
      :submitText="getSubmitText">

      <ValidationObserver ref="form">
        <form action autocomplete="nope">
            <div class="form-items">
                <label class="block text font-medium leading-5" for="name">
                    Nombre
                </label>

                <ValidationProvider rules="required" v-slot="{errors}">
                    <div class="mt-1 rounded-md shadow-sm">
                        <input 
                            placeholder="¡Mi nuevo sitio!"
                            v-model="form.name"
                            v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                            id="name" 
                            type="text">
                    </div>

                    <small class="text-red-600">{{ errors[0] }}</small>
                </ValidationProvider>
            </div>

            <div class="form-items mt-5">
                <label class="block text font-medium leading-5" for="target">
                    Host
                </label>

                <ValidationProvider rules="required" v-slot="{errors}">
                    <div class="mt-1 rounded-md shadow-sm">
                        <input 
                            placeholder="127.0.0.1"
                            v-model="form.target"
                            v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                            id="target" 
                            type="text">
                    </div>

                    <small class="text-red-600">{{ errors[0] }}</small>
                </ValidationProvider>
            </div>            

            <div class="form-items mt-5">
                <label class="block text font-medium leading-5" for="oid">
                    OID
                </label>

                <ValidationProvider rules="required" v-slot="{errors}">
                    <div class="mt-1 rounded-md shadow-sm">
                        <input 
                            placeholder="1.1.1.1.1.2.3"
                            v-model="form.oid"
                            v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                            id="oid" 
                            type="text">
                    </div>

                    <small class="text-red-600">{{ errors[0] }}</small>
                </ValidationProvider>
            </div>

            <div class="form-items mt-5">
                <label class="block text font-medium leading-5" for="data_type">
                    Tipo de dato
                </label>

                <div class="mt-1 rounded-md">
                    <ValidationProvider rules="required" v-slot="{errors}">
                        <v-select
                            class="m:text-sm sm:leading-5 rounded-md"
                            :options="[{code:'bandwidth_in', name:'Ancho de banda entrante'}, { code:'bandwidth_out', name:'Ancho de banda saliente'}, {code:'cpu_use', name:'Uso de CPU'}, {code:'memory_free', name:'Memoria libre'}]"
                            :reduce="code => code.code" 
                            label="name"
                            placeholder="Seleccionar"
                            v-model="form.data_type"
                            v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border border-gray-300 shadow-sm'"
                            id="data_type">

                            <span slot="no-options" @click="$refs.select.open = false">
                              Sin opciones disponibles
                            </span>
                        </v-select>

                        <small class="text-red-600">{{ errors[0] }}</small>
                    </ValidationProvider>
                </div>  
            </div>

            <div class="form-items mt-5">
                <label class="block text font-medium leading-5" for="community">
                    Community
                </label>

                <ValidationProvider rules="required" v-slot="{errors}">
                    <div class="mt-1 rounded-md shadow-sm">
                        <input 
                            placeholder="community name"
                            v-model="form.community"
                            v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                            id="community" 
                            type="text">
                    </div>

                    <small class="text-red-600">{{ errors[0] }}</small>
                </ValidationProvider>
            </div>

             <div class="form-items mt-5">
                <label class="block text font-medium leading-5" for="checkInterval">
                    Intervalo de chequeo (en minutos)
                </label>

                <ValidationProvider rules="required|min:1" v-slot="{errors}">
                    <div class="mt-1 rounded-md shadow-sm">
                        <input 
                            placeholder="1"
                            v-model="form.interval"
                            v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border focus:border-gray-500'"
                            class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                            id="checkInterval" 
                            type="number">
                    </div>

                    <small class="text-red-600">{{ errors[0] }}</small>
                </ValidationProvider>
            </div>

            <ModalCollapsable title="Más opciones">
              <div class="form-items">
                  <label class="block text font-medium leading-5" for="description">
                      Descripción (opcional)
                  </label>

                  <div class="mt-1 rounded-md shadow-sm">
                      <textarea
                          rows="c5"
                          v-model="form.description"
                          class="block w-full resize-none px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 border focus:border-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                          id="description">
                      </textarea>
                  </div>
              </div>

              <div class="form-items mt-5">
                  <label class="block text font-medium leading-5" for="snmp">
                      Version (opcional)
                  </label>

                  <div class="mt-1 rounded-md">
                      <ValidationProvider v-slot="{errors}">
                          <v-select
                              class="m:text-sm sm:leading-5 rounded-md"
                              :options="[{code: 'Version1', name: 'Version1'}, { code: 'Version2', name: 'Version2'}]"
                              :reduce="code => code.code" 
                              label="name"
                              placeholder="Seleccionar"
                              v-model="form.version"
                              v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border border-gray-300 shadow-sm'"
                              id="snmp">

                              <span slot="no-options" @click="$refs.select.open = false">
                                Sin opciones disponibles
                              </span>
                          </v-select>

                          <small class="text-red-600">{{ errors[0] }}</small>
                      </ValidationProvider>
                  </div>  
              </div>

              <div class="form-items mt-5">
                  <label class="block text font-medium leading-5" for="adressingType">
                      Tipo Direccionamiento (opcional)
                  </label>

                  <div class="mt-1 rounded-md">
                      <ValidationProvider v-slot="{errors}">
                          <v-select
                              class="m:text-sm sm:leading-5 rounded-md"
                              :options="[{code: 0, name: 'IPv4'}, { code: 1, name: 'IPv6'}]"
                              :reduce="code => code.code" 
                              label="name"
                              placeholder="Seleccionar"
                              v-model="form.ipv6"
                              v-bind:class="errors[0] ? 'border-red-500 focus:border-red-500 border' : 'border border-gray-300 shadow-sm'"
                              id="adressingType">

                              <span slot="no-options" @click="$refs.select.open = false">
                                Sin opciones disponibles
                              </span>
                          </v-select>

                          <small class="text-red-600">{{ errors[0] }}</small>
                      </ValidationProvider>
                  </div>  
              </div>   

              <div class="form-items mt-5">
                  <label class="block text font-medium leading-5" for="port">
                      Puerto Snmp (opcional)
                  </label>

                  <div class="mt-1 rounded-md shadow-sm">
                      <input 
                          v-model="form.port"
                          placeholder="160"
                          class="block w-full px-3 py-2 border-gray-300 rounded-md placeholder-gray-500 border focus:border-gray-500 focus:outline-none transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                          id="port"
                          type="number">
                  </div>
              </div>                        

            <div class="form-items mt-5">
                <label class="block text font-medium leading-5" for="alertChannel">
                  Canal de alerta (opcional)
                </label>

                <div class="mt-1 rounded-md">
                  <v-select
                      multiple
                      class="m:text-sm sm:leading-5 rounded-md border border-gray-300 shadow-sm"
                      :options="['1', '2', '3']"
                      placeholder="Seleccion múltiple"
                      v-model="form.alert_channel"
                      id="alertChannel">

                      <span slot="no-options" @click="$refs.select.open = false">
                        Sin opciones disponibles
                      </span>
                  </v-select>
                </div>  
            </div>

            </ModalCollapsable>

        </form>
      </ValidationObserver>
    </Modal>

    <Confirm
      v-if="showConfirm"
      :isLoading="isLoading"
      submitText="Eliminar"
      type="error"
      title="¿Deseas eliminar este elemento?"
      description="El registro se eliminará de forma permanente. Esta acción no se puede deshacer."
      @onSubmit="handleDelete(idSelected)" 
      @onCancel="disableConfirm"/>
  </div>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";
import responsesCode from '~/lowdb/responsesCode';
import { mapGetters } from 'vuex';

export default {
  layout: 'client',
  middleware: 'auth',

  head() {
    return {
      title: `Verificaciones SNMP | AilynMS`
    }
  },

  components: {
    ValidationObserver,
    ValidationProvider
  },

  data() {
    return {
      searchText: null,
      isLoading: false,
      isEmpty: false,
      showSkeleton: true,
      showModal: false,
      showConfirm: false,
      editMode: false,
      records: {},
      idSelected: null,
      postMode: true,
      form: {
        name: null,
        description: null,
        status: 1,
        ipv6: 0,
        version: 'Version1',
        port: null,
        community: null,
        target: '',
        oid: '',
        data_type: null,
        interval: 1,
        alert_channel: null,
        tags: null
      },
      page: 1,
      meta: {
        last_page: 1,
        current_page: 1
      },
      error: {
        status: false,
        message: null
      }
    }
  },

  computed: {
    ...mapGetters(['getTenant']),

    resCode() {
      return responsesCode; 
    },

    searchFilter() {
      if(this.records.length) {
        return this.records.filter(val => {
            if(this.searchText) return val.name.toLowerCase().includes(this.searchText.toLowerCase());
            else return val;
        }); 
      }

      return {};
    },

    getSubmitText() {
      if(this.editMode) return 'Actualizar cambios';
      else return 'Guardar cambios';
    },

    getModalTitle(){
      if(this.editMode) return 'Modificar Verificación SNMP';
      else return 'Añadir Verificación SNMP';
    }
  },

  mounted() {
    this.getRecords();
  },

  methods: {
    handleSearch(val) {
      this.searchText = val;
    },

    toggleModal() {
      if(!this.showModal) {
        this.resetForm();
        return this.showModal = true;
      }

      this.resetForm();
      if(this.editMode) this.editMode = false;
      return this.showModal = false;
    },

    resetForm() {
      this.form = {};
      return this.form.status = 1;
    },

    async handleStatus(item) {
      this.form = Object.assign({}, item);
      let url =`${this.$auth.user.tenant.slug}/verifications/snmp-verifications/${this.form.id}`;

      try {
        const res = await this.$axios.$put(url, this.form);
        return this.$toast.success('El registro se ha actualizado correctamente.');

      } catch(err) {
        return this.$toast.error('Ha ocurrido un error al intentar actualizar el registro.'); 
      }
    },

    handleEdit(item) {
      this.idSelected = item.id;
      this.form = Object.assign({}, item);
      this.showModal = true;
      this.editMode = true;
      return this.postMode = false;
    },

    handleSave() {
      return this.handleSubmit();
    },

    async handleSubmit() {
      this.isLoading = true;
      this.error.status = false;
      const isCompleted = await this.$refs.form.validate();
      
      if(isCompleted) {
        let url =`${this.$auth.user.tenant.slug}/verifications/snmp-verifications`;

        try {
          let res;
          
          //  Fix Graphics error
          if(this.form.response_codes === null || !this.form.response_codes) {
            this.form.response_codes = [200];
          }

          if(this.postMode) res = await this.$axios.$post(url, this.form);
          else res = await this.$axios.$put(`${url}/${this.idSelected}`, this.form);

          this.showModal = false;
          this.isLoading = false;
          this.$toast.success('Registro añadido/actualizado correctamente.');
          this.getRecords();

          /* Post error fix */
          this.editMode = false;
          return this.postMode = true;

        } catch(err) {
          this.isLoading = false;

          if(err.response) this.handleError(err.response.data); 
          else console.log(err);
        }
      }
      
      return this.isLoading = false;
    },

    handleDelete(id) {
      this.isLoading = true;
      const url = `${this.getTenant.slug}/verifications/snmp-verifications/${id}`;
      
      this.$axios.$delete(url)
        .then(res => {
          this.$toast.success('Registro eliminado correctamente.');
          this.getRecords()
        })
        .catch(err => this.$toast.error('Ha ocurrido un error al intentar eliminar el registro.'));

      this.isLoading = false;
      return this.showConfirm = false;
    },

    getRecords() {
      const url = `${this.getTenant.slug}/verifications/snmp-verifications?page=${this.meta.current_page}`;

      this.$axios.$get(url)
        .then(res => {
          this.showSkeleton = false;
          this.records = res.data;
          this.meta = res.meta;
          
          if(!this.records.length) return this.isEmpty = true;
          else return this.isEmpty = false;
        })
        .catch(error => {
          this.showSkeleton = false;
          this.error.status = true;
          return this.error.message = `¡${error}!`;
        })
    },

    switchPage(page) {
      this.meta.current_page = page;
      return this.getRecords();
    },

    handleError(err) {
      if(err.message === 'No available dkron agent') {
        this.$toast.error('Ha ocurrido un error con los servidores.')
      }
    },

    enableConfirm(id) {
      this.idSelected = id;
      return this.showConfirm = true;
    },

    disableConfirm() {
      this.showConfirm = false;
    }

  }
}
</script>
