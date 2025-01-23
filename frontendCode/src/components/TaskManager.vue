<template>
    <div class="min-h-screen bg-gray-100 flex flex-col items-center justify-center p-4">
        <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">
            <h1 class="text-3xl font-bold text-green-500 mb-6 text-center">Task Manager</h1>

            <!-- Error Message -->
            <p v-if="error" class="my-2 text-red-500 text-center">
                {{ error }}
            </p>

            <!-- Task Creation Form -->
            <form @submit.prevent="createTask" class="space-y-4">
                <input v-model="newTask.title" placeholder="Task Title" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent" />
                <label class="flex items-center space-x-2">
                    <input v-model="newTask.completed" type="checkbox" class="form-checkbox h-5 w-5 text-green-500" />
                    <span>Completed</span>
                </label>
                <button type="submit"
                    class="w-full bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition duration-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    Create Task
                </button>
            </form>

            <!-- Task List -->
            <div class="mt-8">
                <h2 class="text-2xl font-bold text-green-500 mb-4">Tasks</h2>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-green-500 text-white">
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">Completed</th>
                            <th class="px-4 py-2">Source</th>
                            <th class="px-4 py-2">ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="task in tasks" :key="task.id" class="bg-gray-50 even:bg-white">
                            <td class="px-4 py-2 border border-gray-200 text-center">{{ task.title }}</td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                <input type="checkbox" :checked="task.completed" disabled
                                    class="form-checkbox h-5 w-5 text-green-500" />
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                {{ task.external_id ? 'External' : 'Local' }}
                            </td>
                            <td class="px-4 py-2 border border-gray-200 text-center">
                                {{ task.external_id || task.id }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const newTask = ref({
    title: '',
    completed: false,
});

const tasks = ref([]);
const error = ref('');

const baseUrl = "http://localhost:8000";

// Fetch tasks on component mount
onMounted(async () => {
    try {
        const response = await fetch(baseUrl+'/api/tasks');
        const data = await response.json();
        tasks.value = [...data.local_tasks, ...data.external_tasks];
    } catch (err) {
        error.value = 'Please check your internet connection or try again later. If the issue persists, something went wrong on our end.';
    }
});

// Create a new task
const createTask = async () => {
    const response = await fetch(baseUrl+'/api/tasks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(newTask.value),
    });

    if (response.ok) {
        const task = await response.json();
        tasks.value.push(task);
        newTask.value = { title: '', completed: false };
    }
};
</script>