<script lang="ts" setup>
  import { Dialog } from '@ark-ui/vue/dialog';
  import { useStore } from '../store';
  import sortBy from 'lodash/sortBy';
  import { Section } from '../types';

  const store = useStore();
  const sectionsDialogOpened = ref(false);
  const search = ref('');
  const sections = sortBy(window.ThemeEditor.availableSections(), ['name'], ['asc']);
  const isJsonTemplate = computed(() => !!store.themeData.source);

  const availableSections = computed(() => {
    return sections.filter((section) => {
      const disabled = section.disabledOn.some((pattern) => matchPattern(pattern, store.themeData.template));
      if (disabled) {
        return false;
      }

      return section.enabledOn.some((pattern) => matchPattern(pattern, store.themeData.template));
    });
  });

  const filteredSections = computed(() => {
    if (!search.value) {
      return availableSections.value;
    }

    const regex = new RegExp(search.value, 'gi');
    return availableSections.value.filter((section) => {
      return (
        regex.test(section.slug) ||
        regex.test(section.name) ||
        regex.test(section.description) ||
        regex.test(section.previewDescription)
      );
    });
  });

  const groupedByVendor = computed(() => {
    const grouped: Record<string, Section[]> = {};

    filteredSections.value.forEach((section) => {
      let vendor = 'default';

      if (section.slug.includes('::')) {
        vendor = section.slug.split('::')[0];
      }

      if (!grouped[vendor]) {
        grouped[vendor] = [];
      }

      grouped[vendor].push(section);
    });

    return grouped;
  });

  const currentTemplate = computed(() => {
    return store.templates.find((template) => {
      return template.template === store.themeData.template;
    });
  });

  function matchPattern(pattern: string, value: string) {
    const regex = new RegExp('^' + pattern.replace(/\*/g, '.*') + '$');
    return regex.test(value);
  }

  function toggleSectionsDialog() {
    sectionsDialogOpened.value = !sectionsDialogOpened.value;
  }

  function onContentSectionsReorder(order: string[]) {
    store.setContentSectionsOrder(order);
  }

  function onToggleSection(sectionId: string) {
    store.toggleSection(sectionId);
  }

  function onRemoveSection(sectionId: string) {
    store.removeSection(sectionId);
  }

  function onActivateSection(sectionId: string) {
    store.activateSection(sectionId);
  }

  function onDeactivateSection(sectionId: string) {
    store.deactivateSection(sectionId);
  }

  function onAddSection(section: Section) {
    toggleSectionsDialog();
    store.addNewSection(section);
  }
</script>

<template>
  <div class="w-full h-full flex flex-col">
    <header class="flex none px-4 py-3 border-b font-medium">
      <!-- todo: translate -->
      <h2
        class="flex items-center gap-2"
        v-if="currentTemplate"
      >
        <span
          v-html="currentTemplate.icon"
          class="h-4 w-4"
        />
        {{ currentTemplate?.label }}
      </h2>
    </header>
    <div class="flex-1 overflow-y-auto">
      <SectionsGroup
        static
        :title="$t('Layout Header Sections')"
        :sections="store.beforeContentSections"
        @toggleSection="onToggleSection"
        @activateSection="onActivateSection"
        @deactivateSection="onDeactivateSection"
      />
      <hr />

      <SectionsGroup
        :title="$t('Template Sections')"
        :order="store.contentSectionsOrder"
        :sections="store.contentSections"
        :can-add-section="isJsonTemplate"
        @reorder="onContentSectionsReorder"
        @reordering="store.reorderingContentSections"
        @addSection="toggleSectionsDialog"
        @toggleSection="onToggleSection"
        @removeSection="onRemoveSection"
        @activateSection="onActivateSection"
        @deactivateSection="onDeactivateSection"
      />
      <hr />
      <SectionsGroup
        static
        :title="$t('Layout Footer Sections')"
        :sections="store.afterContentSections"
        @toggleSection="onToggleSection"
        @activateSection="onActivateSection"
        @deactivateSection="onDeactivateSection"
      />
    </div>
  </div>

  <Dialog.Root
    v-model:open="sectionsDialogOpened"
    lazyMount
  >
    <Teleport to="body">
      <Dialog.Backdrop class="fixed h-screen w-screen left-0 top-0 backdrop-blur-sm bg-gray-700/30" />
      <Dialog.Positioner class="flex fixed inset-0 items-center justify-center">
        <Dialog.Content class="bg-white shadow rounded-md flex flex-col w-full max-w-2xl h-3/4 overflow-hidden">
          <header class="flex-none h-12 border-b border-gray-200 bg-gray-100 flex gap-3 px-4 items-center justify-between">
            <Dialog.Title>{{ $t('Add new section') }}</Dialog.Title>
            <Dialog.CloseTrigger class="cursor-pointer rounded-lg p-0.5 text-neutral-700 hover:bg-neutral-300">
              <i-heroicons-x-mark class="w-5 h-5" />
            </Dialog.CloseTrigger>
          </header>
          <section class="flex-1 flex flex-col overflow-hidden">
            <div class="flex items-center gap-3 px-3 border-b py-2 focus-within:ring focus-within:ring-gray-700">
              <i-heroicons-magnifying-glass class="w-4 h-4" />
              <input
                v-model="search"
                type="text"
                name="search"
                class="appearance-none block w-full outline-none"
                :placeholder="$t('Search a section')"
              />
            </div>
            <div class="flex-1 p-6 overflow-y-auto">
              <div
                v-for="(sections, vendor) in groupedByVendor"
                :key="vendor"
                class="mb-4"
              >
                <h4 class="sticky capitalize mb-2 font-medium">{{ $t('From') }} {{ vendor.replace('-', ' ') }}</h4>
                <div class="grid grid-cols-2 gap-6">
                  <div
                    v-for="section in sections"
                    :key="section.slug"
                    class="rounded cursor-pointer shadow hover:shadow-lg"
                    @click="onAddSection(section)"
                  >
                    <div class="aspect-[16/9] bg-gray-100">
                      <img
                        v-if="section.previewImageUrl"
                        :src="section.previewImageUrl"
                        :alt="section.name"
                        class="h-full w-full object-cover object-center"
                      />
                    </div>
                    <div class="text-center p-3">
                      <h3 class="uppercase text-xs font-semibold">
                        {{ section.name }}
                      </h3>
                      <p class="text-xs line-clamp-2">
                        {{ section.previewDescription }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </Dialog.Content>
      </Dialog.Positioner>
    </Teleport>
  </Dialog.Root>
</template>
