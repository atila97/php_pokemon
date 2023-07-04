<template>
  <div class="stat-row">
    <span class="stat-name">{{ name }}</span>
    <span class="stat-value">{{ value }}</span>
    <span class="stat-progress">
      <div :style="{ width: `${value * 2.5}px` }" :class="['progress', getClass()]"></div>
    </span>
  </div>
</template>

<script setup>
const props = defineProps({
  name: {},
  value: {},
});

const getClass = () => {
  if (props.value <= 50) {
    return 'danger';
  } else if (props.value <= 80) {
    return 'warning';
  } else {
    return 'success';
  }
}
</script>
