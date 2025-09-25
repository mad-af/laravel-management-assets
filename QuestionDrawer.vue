<template>
  <div v-if="isOpen" class="drawer drawer-end">
    <input id="survey-drawer-toggle" type="checkbox" class="drawer-toggle" :checked="isOpen" />
    <div class="drawer-content">
      <!-- This is where the main content would be, but we're using this as a component -->
    </div>
    <div class="drawer-side">
      <label for="survey-drawer-toggle" aria-label="close sidebar" class="z-40 drawer-overlay"
        @click="closeDrawer"></label>
      <div class="overflow-visible relative z-50 p-3 w-80 min-h-full bg-base-200 text-base-content">
        <!-- Drawer Header -->
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-base font-semibold">{{ title }}</h3>
          <button class="btn btn-xs btn-circle btn-ghost" @click="closeDrawer">
            <X :size="14" />
          </button>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-3">

          <!-- Section Field -->
          <div class="form-control">
            <label class="text-sm label">
              <span class="label-text">Section Survey</span>
              <span class="label-text-alt text-error">*</span>
            </label>
            <select v-model="form.section_id" class="w-full select select-bordered select-sm"
              :class="{ 'select-error': errors.section_id }" required>
              <option value="">Select section survey</option>
              <option v-for="section in availableSections" :key="section.id" :value="section.id">
                {{ section.order }}. {{ section.title }}
              </option>
            </select>
            <label v-if="errors.section_id" class="label">
              <span class="text-xs label-text-alt text-error">{{ errors.section_id }}</span>
            </label>
          </div>

          <!-- Question Text Field -->
          <div class="form-control">
            <label class="text-sm label">
              <span class="label-text">Question Text</span>
              <span class="label-text-alt text-error">*</span>
            </label>
            <textarea v-model="form.text" placeholder="Enter question text"
              class="w-full h-20 resize-none textarea textarea-bordered textarea-sm"
              :class="{ 'textarea-error': errors.text }" required></textarea>
            <label v-if="errors.text" class="label">
              <span class="text-xs label-text-alt text-error">{{ errors.text }}</span>
            </label>
          </div>

          <!-- Question Type Field -->
          <div class="form-control">
            <label class="text-sm label">
              <span class="label-text">Question Type</span>
              <span class="label-text-alt text-error">*</span>
            </label>
            <select v-model="form.type" class="w-full select select-bordered select-sm"
              :class="{ 'select-error': errors.type }" required>
              <option value="">Select question type</option>
              <option value="short_text">Short Text</option>
              <option value="long_text">Long Text</option>
              <option value="single_choice">Single Choice</option>
              <option value="multiple_choice">Multiple Choice</option>
              <option value="number">Number</option>
              <option value="date">Date</option>
            </select>
            <label v-if="errors.type" class="label">
              <span class="text-xs label-text-alt text-error">{{ errors.type }}</span>
            </label>
          </div>

          <!-- Order Field -->
          <div class="form-control">
            <label class="text-sm label">
              <span class="label-text">Order</span>
            </label>
            <input type="number" v-model.number="form.order" placeholder="Leave empty for auto-order"
              class="w-full input input-bordered input-sm" :class="{ 'input-error': errors.order }" min="1" />
            <label v-if="errors.order" class="label">
              <span class="text-xs label-text-alt text-error">{{ errors.order }}</span>
            </label>
          </div>

          <!-- Score Weight Field -->
          <div class="form-control">
            <label class="text-sm label">
              <span class="label-text">Score Weight</span>
            </label>
            <input type="number" v-model.number="form.score_weight" placeholder="Score weight (optional)"
              class="w-full input input-bordered input-sm" :class="{ 'input-error': errors.score_weight }" step="0.01"
              min="0" />
            <label v-if="errors.score_weight" class="label">
              <span class="text-xs label-text-alt text-error">{{ errors.score_weight }}</span>
            </label>
          </div>

          <!-- Required Field -->
          <div class="form-control">
            <label class="text-sm label">
              <span class="label-text">Required Question</span>
            </label>
            <div class="mt-1 text-xs cursor-pointer">
              <label class="cursor-pointer">
                <input type="checkbox" v-model="form.required" class="checkbox checkbox-sm" />
                <span class="ml-2 label-text">Mark this question as required for survey completion</span>
              </label>
            </div>
          </div>

          <!-- Choices Field (for single_choice and multiple_choice) -->
          <div v-if="form.type === 'single_choice' || form.type === 'multiple_choice'" class="form-control">
            <div class="space-y-3">
              <div v-for="(choice, index) in form.choices" :key="index">
                <fieldset class="p-4 border fieldset bg-base-200 border-base-300 rounded-box">
                  <legend class="flex justify-between items-center w-full fieldset-legend">
                    <span>Choice {{ index + 1 }}</span>
                    <button v-if="form.choices.length > 1" type="button" @click="removeChoice(index)"
                      class="ml-2 btn btn-xs btn-ghost btn-circle text-error">
                      <X :size="12" />
                    </button>
                  </legend>

                  <div class="space-y-3">
                    <div>
                      <label class="label">
                        <span class="label-text">Label</span>
                        <span class="label-text-alt text-error">*</span>
                      </label>
                      <input v-model="choice.label" type="text" placeholder="Choice label"
                        class="w-full input input-bordered input-sm" required />
                    </div>

                    <div>
                      <label class="label">
                        <span class="label-text">Value</span>
                      </label>
                      <input v-model="choice.value" type="text" placeholder="Choice value (optional)"
                        class="w-full input input-bordered input-sm" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                      <div>
                        <label class="label">
                          <span class="label-text">Score</span>
                        </label>
                        <input v-model.number="choice.score" type="number" placeholder="Score weight (optional)"
                          class="w-full input input-bordered input-sm" step="0.01" />
                      </div>

                      <div>
                        <label class="label">
                          <span class="label-text">Order</span>
                        </label>
                        <input v-model.number="choice.order" type="number" placeholder="Leave empty for auto-order"
                          class="w-full input input-bordered input-sm" min="1" />
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>

            <button type="button" @click="addChoice" class="mt-3 w-full btn btn-sm ">
              Add Choice
            </button>

            <label v-if="errors.choices" class="label">
              <span class="text-xs label-text-alt text-error">{{ errors.choices }}</span>
            </label>
          </div>

          <!-- Section ID (Hidden field for API) -->
          <input type="hidden" v-model="form.section_id" />

          <!-- Form Actions -->
          <div class="flex gap-2 pt-3">
            <button type="button" class="flex-1 btn btn-sm" @click="closeDrawer" :disabled="loading">
              Cancel
            </button>
            <button type="submit" class="flex-1 btn btn-primary btn-sm" :disabled="loading">
              <span v-if="loading" class="loading loading-spinner loading-xs"></span>
              {{ buttonText }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch, inject, computed } from 'vue'
import axios from 'axios'
import { X } from 'lucide-vue-next'

// Props
const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false
  },
  title: {
    type: String,
    default: 'Add New Question'
  },
  questionData: {
    type: Object,
    default: () => ({})
  },
  isEditMode: {
    type: Boolean,
    default: false
  },
  sectionId: {
    type: [String, Number],
    default: null
  },
  availableSections: {
    type: Array,
    default: () => []
  }
})

// Emits
const emit = defineEmits(['close', 'submit', 'success'])

// Toast notification
const showToast = inject('showToast', () => { })

// Reactive data
const loading = ref(false)

const form = reactive({
  text: '',
  type: '',
  required: false,
  order: null,
  score_weight: null,
  section_id: '',
  choices: []
})

const errors = reactive({
  text: '',
  type: '',
  required: '',
  order: '',
  score_weight: '',
  section_id: '',
  choices: ''
})

// Computed properties
const buttonText = computed(() => {
  if (loading.value) {
    return props.isEditMode ? 'Updating...' : 'Creating...'
  }
  return props.isEditMode ? 'Update Question' : 'Create Question'
})

// Methods
const closeDrawer = () => {
  emit('close')
}

const resetForm = () => {
  form.text = ''
  form.type = ''
  form.required = false
  form.order = null
  form.score_weight = null
  form.section_id = props.sectionId
  form.choices = []

  // Clear errors
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
}

const validateForm = () => {
  let isValid = true

  // Clear previous errors
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })

  // Text validation
  if (!form.text.trim()) {
    errors.text = 'Question text is required'
    isValid = false
  }

  // Type validation
  if (!form.type) {
    errors.type = 'Question type is required'
    isValid = false
  }

  // Section validation
  if (!form.section_id) {
    errors.section_id = 'Section is required'
    isValid = false
  }

  // Order validation (optional, but must be positive if provided)
  if (form.order !== null && form.order < 1) {
    errors.order = 'Order must be a positive number'
    isValid = false
  }

  // Score weight validation (optional but must be positive if provided)
  if (form.score_weight !== null && form.score_weight < 0) {
    errors.score_weight = 'Score weight must be a positive number'
    isValid = false
  }

  // Choices validation (required for single_choice and multiple_choice)
  if ((form.type === 'single_choice' || form.type === 'multiple_choice')) {
    if (!form.choices || form.choices.length === 0) {
      errors.choices = 'At least one choice is required for this question type'
      isValid = false
    } else {
      // Validate each choice has a label
      const hasEmptyLabel = form.choices.some(choice => !choice.label || !choice.label.trim())
      if (hasEmptyLabel) {
        errors.choices = 'All choices must have a label'
        isValid = false
      }
    }
  }

  return isValid
}

const handleSubmit = async () => {
  if (!validateForm()) {
    return
  }

  loading.value = true

  try {
    // Prepare data for API submission
    const submitData = {
      text: form.text,
      type: form.type,
      required: form.required,
      score_weight: form.score_weight,
      section_id: form.section_id
    }

    // Only include order if it has a value
    if (form.order !== null && form.order !== undefined) {
      submitData.order = form.order
    }

    // Include choices if question type requires them
    if (form.type === 'single_choice' || form.type === 'multiple_choice') {
      submitData.choices = form.choices.map((choice, index) => ({
        label: choice.label,
        value: choice.value || choice.label,
        score: choice.score || 0,
        order: choice.order || (index + 1)
      }))
    }

    let response

    if (props.isEditMode && props.questionData) {
      // Update existing question
      response = await axios.put(`/api/sections/${form.section_id}/questions/${props.questionData.id}`, submitData)
    } else {
      // Create new question
      response = await axios.post(`/api/sections/${form.section_id}/questions`, submitData)
    }

    if (response.data.success) {
      showToast(response.data.message, 'success')
      emit('success', response.data.data)
      emit('close')
      resetForm()
    } else {
      throw new Error(response.data.message || 'Failed to save question')
    }
  } catch (error) {
    console.error('Error submitting question:', error)

    // Handle validation errors
    if (error.response?.status === 422 && error.response?.data?.errors) {
      const validationErrors = error.response.data.errors
      Object.keys(validationErrors).forEach(key => {
        if (errors.hasOwnProperty(key)) {
          errors[key] = validationErrors[key][0]
        }
      })
    } else {
      const errorMessage = error.response?.data?.message || error.message || 'Failed to save question'
      showToast(errorMessage, 'error')
    }
  } finally {
    loading.value = false
  }
}



// Watch for drawer open/close and questionData changes
watch(() => props.isOpen, (newValue) => {
  if (newValue && props.isEditMode && props.questionData) {
    // Fill form with question data for editing
    form.text = props.questionData.text || ''
    form.type = props.questionData.type || ''
    form.required = props.questionData.required || false
    form.order = props.questionData.order || null
    form.score_weight = props.questionData.score_weight || null
    form.section_id = props.questionData.section_id || props.sectionId
    form.choices = props.questionData.choices ? props.questionData.choices.map(choice => ({
      id: choice.id,
      label: choice.label,
      value: choice.value,
      score: choice.score,
      order: choice.order
    })) : []
  } else if (!newValue) {
    resetForm()
  }
})

watch(() => props.questionData, (newValue) => {
  if (props.isOpen && props.isEditMode && newValue) {
    // Fill form with question data when questionData changes
    form.text = newValue.text || ''
    form.type = newValue.type || ''
    form.required = newValue.required || false
    form.order = newValue.order || null
    form.score_weight = newValue.score_weight || null
    form.section_id = newValue.section_id || props.sectionId
    form.choices = newValue.choices ? newValue.choices.map(choice => ({
      id: choice.id,
      label: choice.label,
      value: choice.value,
      score: choice.score,
      order: choice.order
    })) : []
  }
})

// Watch for sectionId changes
watch(() => props.sectionId, (newSectionId) => {
  form.section_id = newSectionId
}, { immediate: true })

// Watch for question type changes
watch(() => form.type, (newType) => {
  if (newType === 'single_choice' || newType === 'multiple_choice') {
    if (form.choices.length === 0) {
      addChoice()
    }
  } else {
    form.choices = []
  }
})

// Watch for choices changes and auto-reorder
watch(() => form.choices.length, () => {
  reorderChoices()
}, { deep: true })

// Choice management methods
const reorderChoices = () => {
  form.choices.forEach((choice, index) => {
    choice.order = index + 1
  })
}

const addChoice = () => {
  form.choices.push({
    label: '',
    value: '',
    score: '',
    order: ''
  })
  reorderChoices()
}

const removeChoice = (index) => {
  form.choices.splice(index, 1)
  reorderChoices()
}
</script>