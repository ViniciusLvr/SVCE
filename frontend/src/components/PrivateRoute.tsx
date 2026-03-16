import { Navigate } from 'react-router-dom'
import { useAuth } from '../context/AuthContext'

import type { ReactElement } from 'react'

type PrivateRouteProps = {
  children: ReactElement
}

function PrivateRoute({ children }: PrivateRouteProps) {
  const { token } = useAuth()
  if (!token) {
    return <Navigate to="/login" replace />
  }
  return children
}

export default PrivateRoute
