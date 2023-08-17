import { useEffect, useState } from 'react'
import axiosClient from '../axios-client.js'
import { Link } from 'react-router-dom'
import { useStateContext } from '../contexts/ContextProvider'

export default function Users () {
  const [users, setUsers] = useState([])
  const [loading, setLoading] = useState(false)
  const { setNotification } = useStateContext()

  useEffect(() => {
    getUsers()
  }, [])

  const getUsers = () => {
    setLoading(true)
    axiosClient.get('/users')
      .then(({ data }) => {
        setUsers(data.data)
      })
      .finally(() => {
        setLoading(false)
      })
  }

  const handleDelete = (id) => {
    if (!window.confirm('Are you sure you want to delete this user?')) {
      return
    }

    axiosClient.delete(`/users/${id}`)
      .then(() => {
        setNotification('User was successfully deleted')
        getUsers()
      })
      .catch(() => {

      })
  }

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <h1>Users</h1>
        <Link to='/users/new' className='btn-add'>Add new</Link>
      </div>
      <div className='card animated fadeInDown'>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Email</th>
              <th>Create Date</th>
              <th>Actions</th>
            </tr>
          </thead>

          {loading && (
            <tbody>
              <tr>
                <td colSpan='5' className='text-center'>
                  Loading...
                </td>
              </tr>
            </tbody>
          )}

          {!loading && (
            <tbody>
              {users.map(({ id, name, email, created_at: createAt }) => (
                <tr key={id}>
                  <td>{id}</td>
                  <td>{name}</td>
                  <td>{email}</td>
                  <td>{createAt}</td>
                  <td>
                    <Link to={`/users/${id}`} className='btn-edit' style={{ marginRight: 3 }}>Edit</Link>
                    <button onClick={() => handleDelete(id)} className='btn-delete'>Delete</button>
                  </td>
                </tr>
              ))}
            </tbody>
          )}
        </table>
      </div>
    </div>
  )
}
