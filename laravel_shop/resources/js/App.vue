<script setup>
import { ref, onMounted } from 'vue';

const items = ref([]);
const loading = ref(true);
const error = ref(null);

const fetchItems = async () => {
  try {
    const response = await fetch('http://localhost:3000/api/items');
    if (!response.ok) throw new Error('Error al conectar con el backend');
    items.value = await response.json();
  } catch (err) {
    error.value = err.message;
    console.error(err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchItems();
});
</script>

<template>
  <header>
    <img alt="Vue logo" class="logo" src="./assets/logo.svg" width="125" height="125" />

    <div class="wrapper">
      <h1>Mi Proyecto Final</h1>
      <p>Estado del Backend: <span :class="error ? 'error' : 'success'">{{ error ? 'Desconectado' : 'Conectado' }}</span></p>
    </div>
  </header>

  <main>
    <section class="items-section">
      <h2>Items desde la Base de Datos:</h2>
      
      <div v-if="loading">Cargando datos...</div>
      
      <div v-else-if="error" class="error-msg">
        {{ error }}. Asegúrate de que el backend esté corriendo en el puerto 3000.
      </div>

      <ul v-else-if="items.length > 0">
        <li v-for="item in items" :key="item.id" class="item-card">
          <h3>{{ item.name }}</h3>
          <p>{{ item.description }}</p>
          <small>Creado: {{ new Date(item.created_at).toLocaleString() }}</small>
        </li>
      </ul>

      <p v-else>No se encontraron items en la base de datos.</p>
    </section>
  </main>
</template>

<style scoped>
header {
  line-height: 1.5;
  margin-bottom: 2rem;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

.success { color: #42b883; font-weight: bold; }
.error { color: #ff6666; font-weight: bold; }
.error-msg { background: #ff666622; padding: 1rem; border-radius: 8px; border: 1px solid #ff6666; }

.items-section {
  padding: 1rem;
}

.item-card {
  list-style: none;
  background: #2c3e50;
  color: white;
  padding: 1rem;
  margin: 1rem 0;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.item-card h3 {
  margin: 0 0 0.5rem 0;
  color: #42b883;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }
}
</style>