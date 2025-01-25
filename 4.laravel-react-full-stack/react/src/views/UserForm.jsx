import { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { useStateContext } from '../contexts/ContextProvider.jsx'
import axiosClient from '../axios-client.js'

export default function UserForm () {
  const navigate = useNavigate()
  const { id: paramId } = useParams()

  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState(false)
  const { setNotification } = useStateContext()
  const [user, setUser] = useState({
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
  })

  useEffect(() => {
    if (!paramId) return

    setLoading(true)
    axiosClient.get(`/users/${paramId}`)
      .then(({ data }) => {
        setUser(data)
      })
      .finally(() => {
        setLoading(false)
      })
  }, [])

  const handleSubmit = (e) => {
    e.preventDefault()
    if (user.id) {
      axiosClient.put(`/users/${user.id}`, user)
        .then(() => {
          setNotification('User was successfully updated')
          navigate('/users')
        })
        .catch(err => {
          const response = err.response
          if (response && response.status === 422) {
            setErrors(response.data.errors)
          }
        })
    } else {
      axiosClient.post('/users', user)
        .then(() => {
          setNotification('User was successfully created')
          navigate('/users')
        })
        .catch(err => {
          const response = err.response
          if (response && response.status === 422) {
            setErrors(response.data.errors)
          }
        })
    }
  }

  return (
    <>
      <h1>
        {user.id
          ? `Update User: ${user.name}`
          : 'New User'}
      </h1>

      <div className='card animated fadeInDown'>
        {loading && (
          <div className='text-center'>Loading...</div>
        )}

        {errors && (
          <div className='alert'>
            {Object.keys(errors).map(key => (
              <p key={key}>{errors[key][0]}</p>
            ))}
          </div>
        )}

        {!loading && (
          <form onSubmit={handleSubmit}>
            <input
              onChange={({ target }) => setUser({ ...user, name: target.value })}
              value={user.name}
              type='text'
              placeholder='Name'
            />
            <input
              onChange={({ target }) => setUser({ ...user, email: target.value })}
              value={user.email}
              type='email'
              placeholder='Email'
            />
            <input
              onChange={({ target }) => setUser({ ...user, password: target.value })}
              type='password'
              placeholder='Password'
            />
            <input
              onChange={({ target }) => setUser({ ...user, password_confirmation: target.value })}
              type='password'
              placeholder='Password Confirmation'
            />
            <button type='submit' className='btn'>Save</button>
          </form>
        )}
      </div>
    </>
  )
}
