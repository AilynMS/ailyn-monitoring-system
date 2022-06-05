<template>
    <nav class="pagination wrapper flex justify-center mt-6 sm:my-auto">
        <ul class="flex mx-1 my-auto">
            <li>
                <a
                    :disabled="meta.current_page === 1"
                    @click.prevent="switched(meta.current_page - 1)"
                    class="pagination-item-accent mr-1"
                    href="#">
                    <fa icon="angle-left" />
                </a>
            </li>

            <template v-if="section > 1">
                <li class="mx-1">
                    <a 
                        @click.prevent="switched(1)" 
                        class="pagination-item hover:text-gray-800" 
                        href="#">
                        1
                    </a>
                </li>

                <li class="mx-1">
                    <a 
                    @click.prevent="goBackASection()" 
                    class="pagination-item hover:text-gray-800" 
                    href="#">

                    <span class="pagination-ellipsis">
                        &hellip;
                    </span>
                    </a>
                </li>
            </template>

            <li class="mx-1" v-for="page in pages" :key="page + '_pagination'">
                <a
                    v-bind:class="meta.current_page === page ? 'pagination-item-primary' : 'pagination-item'"
                    href="#"
                    @click.prevent="switched(page)">
                    {{ page }}
                </a>
            </li>

            <template v-if="section < sections">
                <li class="mx-1">
                    <a 
                        @click.prevent="goForwardASection()" 
                        class="pagination-item hover:text-gray-800" 
                        href="#">

                        <span class="pagination-ellipsis">&hellip;</span>
                    </a>
                </li>

                <li class="mx-1">
                    <a
                        @click.prevent="switched(meta.last_page)"
                        class="pagination-item hover:text-gray-800"
                        href="#">
                        {{ meta.last_page }}
                    </a>
                </li>
            </template>

            <li>
                <a
                    @click.prevent="switched(meta.current_page + 1)"
                    class="pagination-item-accent ml-1"
                    href="#"
                    :disabled="meta.current_page === this.meta.last_page">
                    <fa icon="angle-right" />
                </a>
            </li>
        </ul>

    </nav>
</template>

<script>
import _ from 'lodash';

export default {
  name: "pagination",

  props: {
    meta: {
      type: Object,
      required: true
    },
    per_section: {
      type: Number,
      default: 7
    }
  },

  computed: {
    sections() {
      return Math.ceil(this.meta.last_page / this.per_section);
    },

    section() {
      return Math.ceil(this.meta.current_page / this.per_section);
    },

    lastPage() {
      let lastPage = (this.section - 1) * this.per_section + this.per_section;
      if (this.section == this.sections) lastPage = this.meta.last_page;

      return lastPage;
    },

    pages() {
      return _.range(
        (this.section - 1) * this.per_section + 1,
        this.lastPage + 1
      );
    }
  },
  methods: {
    switched(page) {
        if (page <= 0 || page > this.meta.last_page || page === this.meta.current_page) {
            return false;
        }

        this.$emit("pagination:switched", page);
    },

    goBackASection() {
      this.switched(this.firstPageOfSection(this.section - 1));
    },

    goForwardASection() {
      this.switched(this.firstPageOfSection(this.section + 1));
    },

    firstPageOfSection(section) {
      return (section - 1) * this.per_section + 1;
    }
  }
};
</script>
