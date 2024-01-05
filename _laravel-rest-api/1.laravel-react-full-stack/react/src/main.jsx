import React from 'react'
import ReactDOM from 'react-dom/client'
import { ContextProvider } from './contexts/ContextProvider'
import { RouterProvider } from 'react-router-dom'
import router from './router.jsx'
import './index.css'

ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <ContextProvider>
      <RouterProvider router={router} />
    </ContextProvider>
  </React.StrictMode>
)
