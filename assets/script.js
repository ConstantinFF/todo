/**
 * @class Model
 *
 * Manages the data of the todo list.
 */
class Model {
    constructor() {
        this.todos = [];
    }

    /**
     * Bind callback when todo list is changed.
     * @param {Function} callback 
     */
    bindTodoListChanged(callback) {
        this.onTodoListChanged = callback;
    }

    /**
     * Bind callback on api error.
     * @param {Function} callback 
     */
    bindHandleError(callback) {
        this.onError = callback;
    }

    /**
     * Execute list changed callback.
     * @param {Array} todos 
     */
    _commit(todos) {
        this.onTodoListChanged(todos);
    }

    /**
     * Get list of todos and set them into the model.
     */
    async getTodos() {
        const response = await this.api('/to-do-list/backend');
        this.todos = response.data;

        this._commit(this.todos);
    }

    /**
     * Submit add todo form.
     * @param {Object} form 
     */
    async addTodo(form) {
        const response = await this.api('/to-do-list/backend', {
            method: 'POST',
            body: new FormData(form),
        });

        this.todos.push(response);
        this._commit(this.todos);
    }

    /**
     * Make api request and handle errors.
     * @param {Array} args 
     */
    async api(...args) {
        const response = await fetch(...args);
        const data = await response.json();

        if (response.status >= 400 && response.status < 600) {
            this.onError(data.error);
            throw new Error(data.error);
        }

        return data;
    }

    /**
     * Delete todo record.
     * @param {number} id 
     */
    async deleteTodo(id) {
        await this.api(`/to-do-list/backend/${id}`, {
            method: 'DELETE',
        });

        this.todos = this.todos.filter(todo => todo.id != id);
        this._commit(this.todos);
    }

    /**
     * Update todo record completed status.
     * @param {number} id 
     * @param {boolean} id 
     */
    async setCompletedTodo(id, is_completed) {
        await this.api(`/to-do-list/backend/${id}`, {
            method: 'PUT',
            body: JSON.stringify({ is_completed }),
        });

        const todoIndex = this.todos.findIndex((todo => todo.id == id));

        this.todos[todoIndex].is_completed = is_completed;
        this._commit(this.todos);
    }

    /**
     * Update todos order.
     * @param {Array<number>} todosOrder 
     */
    async sortTodo(todosOrder) {
        await this.api('/to-do-list/backend', {
            method: 'PUT',
            body: JSON.stringify({ sort: todosOrder }),
        });

        this.todos = this.todos.sort((a, b) => todosOrder.indexOf(a.id) - todosOrder.indexOf(b.id));
        this._commit(this.todos);
    }
}

/**
 * @class View
 *
 * Render Todo list.
 */
class View {
    constructor() {
        this.todoList = this.getElement('#todos');
        this.form = this.getElement('#add-todo');
        this.input = this.form.querySelector('input');
        this.error = this.getElement('#error');

        this.selectedDrag = null;
    }

    /**
     * Find an element from the DOM document.
     * @param {string} selector 
     */
    getElement(selector) {
        const element = document.querySelector(selector);

        return element;
    }

    /**
     * Get a clone from todo template row.
     * @returns {Object} template
     */
    getTemplate() {
        const template = document.querySelector('#todo-row');
        const clone = template.content.cloneNode(true);

        return clone;
    }

    /**
     * Populate todo template with its data.
     * @param {Object} template 
     * @param {Object} todo 
     */
    populateData(template, todo) {
        template.querySelector('div').textContent = todo.title;

        const checkbox = template.querySelector('input[type=checkbox]');
        checkbox.checked = todo.is_completed == 1;
        checkbox.addEventListener('change', event => {
            this.setCompletedTodoHandler(todo.id, event.currentTarget.checked);
        })

        template.querySelector('button.delete')
            .addEventListener('click', () => {
                this.deleteTodoHandler(todo.id);
            })

        const li = template.firstElementChild;

        li.dataset.id = todo.id;

        li.addEventListener('dragstart', (event) => {
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', null);
            this.selectedDrag = event.target;
        });

        li.addEventListener('dragover', (event) => {
            event.target.parentNode.insertBefore(
                this.selectedDrag,
                this._isBefore(this.selectedDrag, event.target)
                    ? event.target
                    : event.target.nextSibling
            );
        })

        li.addEventListener('dragend', (event) => {
            this.selectedDrag = null;

            const todoSort = [].map.call(this.todoList.children, function (event) {
                return event.dataset.id;
            })

            this.sortTodoHandler(todoSort);
        })
    }

    /**
     * Update the list of todos.
     * @param {Array} todos 
     */
    displayTodos(todos) {
        // Delete all nodes
        while (this.todoList.firstChild) {
            this.todoList.removeChild(this.todoList.firstChild);
        }

        todos.forEach(todo => {
            const template = this.getTemplate();
            this.populateData(template, todo);
            this.todoList.append(template);
        })
    }

    /**
     * Display error message.
     * @param {string} error 
     */
    displayError(error) {
        this.error.textContent = error;
        this.error.style = 'display:block';

        setTimeout(function () {
            this.error.textContent = '';
            this.error.style = '';
        }, 10000);
    }

    /**
     * Bind form submit.
     * @param {Function} handler 
     */
    bindAddTodo(handler) {
        this.form.addEventListener('submit', event => {
            event.preventDefault();
            handler(event.currentTarget);
            this.input.value = '';
        })
    }

    /**
     * Check if el1 is before el2.
     * @param {Object} el1 
     * @param {Object} el2 
     */
    _isBefore(el1, el2) {
        let cur;
        if (el2.parentNode === el1.parentNode) {
            for (cur = el1.previousSibling; cur; cur = cur.previousSibling) {
                if (cur === el2) return true;
            }
        }
        return false;
    }
}

/**
 * @class Controller
 *
 * Links the user input and the view output.
 *
 * @param model
 * @param view
 */
class Controller {
    constructor(model, view) {
        this.model = model;
        this.view = view;

        this.model.bindTodoListChanged(this.onTodoListChanged);
        this.model.bindHandleError(this.onError);
        this.view.deleteTodoHandler = this.handleDeleteTodo;
        this.view.setCompletedTodoHandler = this.handleSetCompletedTodo;
        this.view.sortTodoHandler = this.handleSortTodo;
        this.view.bindAddTodo(this.handleAddTodo);

        // Display initial todos
        this.model.getTodos();

    }

    /**
     * Update todos view.
     * @param {Array} todosOrder 
     */
    onTodoListChanged = todos => {
        this.view.displayTodos(todos);
    }

    /**
     * Handle error messages.
     * @param {string} error 
     */
    onError = error => {
        this.view.displayError(error);
    }

    /**
     * Handle todo form submit.
     * @param {Object} form 
     */
    handleAddTodo = form => {
        this.model.addTodo(form);
    }

    /**
     * Handle delete.
     * @param {number} id 
     */
    handleDeleteTodo = id => {
        this.model.deleteTodo(id);
    }

    /**
     * Handle todo completed.
     * @param {number} id 
     * @param {boolean} is_completed 
     */
    handleSetCompletedTodo = (id, is_completed) => {
        this.model.setCompletedTodo(id, is_completed);
    }

    /**
     * Handle sort todo list.
     * @param {Array<number>} todoIds 
     */
    handleSortTodo = (todoIds) => {
        this.model.sortTodo(todoIds);
    }
}

const app = new Controller(new Model(), new View());